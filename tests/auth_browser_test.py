import json
import os
import secrets
import subprocess
from pathlib import Path
from playwright.sync_api import sync_playwright

ROOT = Path(__file__).resolve().parents[1]
OUT = ROOT/'output'/'qa'
URL = 'http://localhost/web-id3/'
username = 'auth_test_' + secrets.token_hex(6)
password = secrets.token_urlsafe(20)
new_password = secrets.token_urlsafe(20)
env = dict(os.environ, SIMINAT_TEST_PASSWORD=password, SIMINAT_TEST_ROLE='guru_bk')
subprocess.run(['php','tests/auth_fixture.php','create',username],check=True,capture_output=True,env=env)
try:
    with sync_playwright() as p:
        browser = p.chromium.launch()
        page = browser.new_page()
        errors = []
        page.on('pageerror',lambda e:errors.append(str(e)))
        page.on('console',lambda m:errors.append(m.text) if m.type=='error' and 'status of 422' not in m.text else None)
        viewports = []
        for width,height in [(1440,900),(1280,720),(768,1024),(390,844),(360,800)]:
            page.set_viewport_size({'width':width,'height':height})
            page.goto(URL+'login.php',wait_until='networkidle')
            assert page.evaluate('document.documentElement.scrollWidth <= innerWidth')
            assert page.locator('h1').count()==1
            assert page.locator('#username').is_visible()
            page.screenshot(path=str(OUT/f'login-{width}.png'),full_page=True)
            viewports.append({'width':width,'horizontalOverflow':False})
        page.locator('[data-help-open]').click()
        assert page.locator('#login-help').evaluate('(d)=>d.open')
        for _ in range(6):
            page.keyboard.press('Tab')
            assert page.evaluate("document.activeElement.closest('#login-help') !== null")
        page.keyboard.press('Escape')
        assert page.locator('[data-help-open]').evaluate('(e)=>document.activeElement===e')
        page.locator('[data-help-open]').click()
        page.locator('#login-help .auth-submit').click()
        page.locator('[data-help-open]').click()
        page.locator('[data-help-close]').first.click()
        page.locator('[data-help-open]').click()
        page.mouse.click(4,4)
        assert not page.locator('#login-help').evaluate('(d)=>d.open')
        page.locator('#password').fill(password)
        page.locator('[data-password-toggle]').click()
        assert page.locator('#password').get_attribute('type')=='text'
        assert page.locator('[data-password-toggle]').get_attribute('aria-pressed')=='true'
        page.locator('[data-password-toggle]').click()
        assert page.locator('#password').get_attribute('type')=='password'
        page.locator('#password').fill('')
        page.locator('[data-submit]').click()
        page.wait_for_load_state('networkidle')
        assert page.locator('#username-error').is_visible()
        assert page.locator('[data-form-alert]').evaluate('(e)=>document.activeElement===e')
        page.screenshot(path=str(OUT/'login-validation-mobile.png'),full_page=True)
        page.locator('#username').fill(username)
        page.locator('#password').fill(password)
        page.locator('[data-submit]').click()
        page.wait_for_url('**/admin/')
        assert page.get_by_role('heading',name='Ruang kerja Anda.').is_visible()
        assert page.locator('#current_password').count()==0
        page.get_by_role('link',name='Ganti kata sandi',exact=True).click()
        page.wait_for_url('**/admin/?page=password')
        assert page.get_by_role('heading',name='Ganti kata sandi.').is_visible()
        page.set_viewport_size({'width':1280,'height':900})
        page.screenshot(path=str(OUT/'change-password.png'),full_page=True)
        page.locator('#current_password').fill(password)
        page.locator('#password').fill(new_password)
        page.locator('#password_confirmation').fill(new_password)
        page.locator('[data-submit]').click()
        page.wait_for_load_state('networkidle')
        assert page.get_by_role('heading',name='Ruang kerja Anda.').is_visible()
        assert page.get_by_text('Guru BK',exact=True).is_visible()
        page.screenshot(path=str(OUT/'workspace.png'),full_page=True)
        page.goto(URL,wait_until='networkidle')
        page.get_by_role('link',name='Panel Saya',exact=True).filter(visible=True).click()
        page.wait_for_url('**/admin/')
        page.get_by_role('button',name='Keluar dari akun').click()
        page.wait_for_url('**/login.php')
        page.locator('.auth-back').click()
        page.wait_for_url(URL)
        page.get_by_role('link',name='Masuk',exact=True).filter(visible=True).click()
        page.wait_for_url('**/login.php')
        page.set_viewport_size({'width':360,'height':800})
        page.goto(URL)
        page.locator('[data-menu-open]').click()
        page.locator('#mobile-menu').get_by_role('link',name='Masuk').click()
        page.wait_for_url('**/login.php')
        page.goto('http://web-id3.test/login.php',wait_until='networkidle')
        assert page.locator('.auth-back').get_attribute('href')=='/'
        assert not errors, errors
        report={'viewports':viewports,'javascriptErrors':errors,'flows':'PASS: landing navigation desktop/mobile, validation, show password, help dialog and keyboard, Guru BK login directly to workspace, optional password change, dynamic navigation, logout, virtual-host paths'}
        (OUT/'auth-browser-results.json').write_text(json.dumps(report,indent=2),encoding='utf-8')
        print(json.dumps(report,indent=2))
        browser.close()
finally:
    subprocess.run(['php','tests/auth_fixture.php','delete',username],check=True,capture_output=True)
