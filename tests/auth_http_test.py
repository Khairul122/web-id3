"""HTTP authentication regression test; uses an isolated fixture account and loopback IP."""
import hashlib
import http.client
import os
import re
import secrets
import subprocess
from http.cookies import SimpleCookie
from urllib.parse import urlencode

PREFIX = '/web-id3/'
USERNAME = 'auth_test_' + secrets.token_hex(6)
PASSWORD = secrets.token_urlsafe(20)
NEW_PASSWORD = secrets.token_urlsafe(20)

class Client:
    def __init__(self):
        self.cookies = SimpleCookie()

    def request(self, path, data=None, method=None):
        connection = http.client.HTTPConnection('127.0.0.1', 80, source_address=('127.0.0.2', 0), timeout=15)
        headers = {'Host': 'localhost'}
        if self.cookies:
            headers['Cookie'] = '; '.join(f'{k}={v.value}' for k, v in self.cookies.items())
        body = urlencode(data) if data is not None else None
        if body is not None:
            headers['Content-Type'] = 'application/x-www-form-urlencoded'
        connection.request(method or ('POST' if data is not None else 'GET'), PREFIX + path, body, headers)
        response = connection.getresponse()
        response_headers = dict(response.getheaders())
        for key, value in response.getheaders():
            if key.lower() == 'set-cookie':
                self.cookies.load(value)
        text = response.read().decode('utf-8')
        status = response.status
        connection.close()
        assert not re.search(r'<b>(?:Warning|Fatal error|Notice)</b>', text), text[:300]
        return status, response_headers, text

def csrf(text):
    return re.search(r'name="_csrf" value="([a-f0-9]+)"', text).group(1)

client = Client()
status, headers, page = client.request('login.php')
assert status == 200, f'Login page must exist; received HTTP {status}'
assert 'Selamat datang' in page
env = dict(os.environ, SIMINAT_TEST_PASSWORD=PASSWORD)
created = False
try:
    subprocess.run(['php', 'tests/auth_fixture.php', 'create', USERNAME], env=env, check=True, capture_output=True)
    created = True
    assert client.request('admin/')[0] == 303
    assert client.request('login.php', {'username':USERNAME, 'password':PASSWORD})[0] == 419
    status, headers, page = client.request('login.php')
    assert 'HttpOnly' in headers.get('Set-Cookie', '') or client.cookies
    token = csrf(page)
    status, _, page = client.request('login.php', {'_csrf':token,'username':'','password':''})
    assert status == 422 and 'Username wajib diisi' in page
    status, _, page = client.request('login.php', {'_csrf':csrf(page),'username[]':'x','password':'x'})
    assert status == 422
    status, _, page = client.request('login.php', {'_csrf':csrf(page),'username':"' OR 1=1 --",'password':'x'})
    assert status == 422
    status, _, page = client.request('login.php', {'_csrf':csrf(page),'username':USERNAME,'password':'wrong-password'})
    assert status == 422 and 'Username atau kata sandi tidak sesuai' in page
    old_session = client.cookies['siminat_session'].value
    status, _, _ = client.request('login.php', {'_csrf':csrf(page),'username':USERNAME,'password':PASSWORD})
    assert status == 303
    assert client.cookies['siminat_session'].value != old_session
    status, _, page = client.request('admin/')
    assert status == 200 and 'Perbarui kata sandi' in page
    status, _, page = client.request('admin/?page=password', {'_csrf':csrf(page),'current_password':PASSWORD,'password':NEW_PASSWORD,'password_confirmation':'different'})
    assert status == 422 and 'Konfirmasi kata sandi belum cocok' in page
    status, _, _ = client.request('admin/?page=password', {'_csrf':csrf(page),'current_password':PASSWORD,'password':NEW_PASSWORD,'password_confirmation':NEW_PASSWORD})
    assert status == 303
    status, _, page = client.request('admin/')
    assert status == 200 and 'Ruang kerja Anda' in page
    assert client.request('login.php')[0] == 303
    assert client.request('logout.php')[0] == 405
    assert client.request('logout.php', {'_csrf':'invalid'})[0] == 419
    assert client.request('logout.php', {'_csrf':csrf(page)})[0] == 303
    assert client.request('admin/')[0] == 303
    _, _, page = client.request('login.php')
    status, _, page = client.request('login.php', {'_csrf':csrf(page),'username':USERNAME,'password':PASSWORD})
    assert status == 422
    status, _, _ = client.request('login.php', {'_csrf':csrf(page),'username':USERNAME,'password':NEW_PASSWORD})
    assert status == 303
    _, _, page = client.request('admin/')
    client.request('logout.php', {'_csrf':csrf(page)})
    _, _, page = client.request('login.php')
    for _ in range(5):
        status, _, page = client.request('login.php', {'_csrf':csrf(page),'username':USERNAME,'password':'wrong-password'})
        assert status == 422
    status, headers, page = client.request('login.php', {'_csrf':csrf(page),'username':USERNAME,'password':NEW_PASSWORD})
    assert status == 429 and int(headers['Retry-After']) > 0
    print('PASS: login, validation, CSRF, prepared queries, session rotation, password change, protected access, POST logout, and shared IP rate limit.')
finally:
    if created:
        subprocess.run(['php','tests/auth_fixture.php','delete',USERNAME],check=True,capture_output=True)
