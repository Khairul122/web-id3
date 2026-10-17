"""Public landing contract, navigation, responsive behavior, and asset checks."""
import json
from pathlib import Path
from playwright.sync_api import sync_playwright

OUT = Path(__file__).resolve().parents[1] / 'output' / 'qa'
URL = 'http://localhost/web-id3/'
SECTIONS = ['beranda', 'rumpun', 'cara-kerja', 'metode', 'panduan']

with sync_playwright() as p:
    browser = p.chromium.launch()
    page = browser.new_page()
    errors, failed_assets = [], []
    page.on('pageerror', lambda e: errors.append(str(e)))
    page.on('console', lambda m: errors.append(m.text) if m.type == 'error' else None)
    page.on('response', lambda r: failed_assets.append([r.url, r.status]) if r.status >= 400 else None)
    page.goto(URL, wait_until='networkidle')
    for section in SECTIONS:
        assert page.locator(f'section#{section}').count() == 1, f'Missing section: {section}'
    assert page.locator('#info-dialog, template[id^="content-"]').count() == 0
    assert page.locator('#metode').get_by_text('Ilustrasi, bukan pohon hasil data siswa', exact=True).count() == 1
    assert page.locator('#metode .stop-list li').count() == 3
    assert page.locator('#cara-kerja [data-journey-step]').count() == 4
    assert page.locator('#rumpun [role="tab"]').count() == 3
    assert page.locator('footer').count() == 1
    assert page.locator('main footer').count() == 0
    assert not page.locator('a[href^="mailto:"], a[href^="tel:"]').count()
    assert page.locator('h1').count() == 1
    assert page.locator('.hero__actions a[href="#rumpun"]').count() == 1
    assert page.locator('.hero__actions a[href="#cara-kerja"]').count() == 1
    assert page.locator('#panduan a[href$="login.php"]').count() == 1
    for tab in page.get_by_role('tab').all():
        tab.click()
        assert tab.get_attribute('aria-selected') == 'true'
        assert page.locator('#' + tab.get_attribute('aria-controls')).is_visible()
        assert page.locator('[role="tabpanel"]:visible').count() == 1
    page.get_by_role('tab').first.focus()
    page.keyboard.press('End')
    assert page.get_by_role('tab').last.get_attribute('aria-selected') == 'true'
    page.keyboard.press('ArrowRight')
    assert page.get_by_role('tab').first.get_attribute('aria-selected') == 'true'
    page.keyboard.press('ArrowLeft')
    assert page.get_by_role('tab').last.get_attribute('aria-selected') == 'true'
    page.keyboard.press('Home')
    assert page.get_by_role('tab').first.get_attribute('aria-selected') == 'true'
    for step in page.locator('[data-journey-step]').all():
        step.click()
        assert step.get_attribute('aria-current') == 'step'
        assert page.locator(step.get_attribute('href')).is_visible()
        assert page.locator('[data-journey-panel]:visible').count() == 1
    for index, checkbox in enumerate(page.locator('[data-checklist] input').all(), 1):
        checkbox.check()
        assert page.locator('.preparation-progress progress').get_attribute('value') == str(index)
    assert 'Semua siap' in page.locator('[data-checklist-status]').inner_text()
    page.locator('[data-checklist] input').first.uncheck()
    assert '3 dari 4' in page.locator('[data-checklist-status]').inner_text()
    page.locator('.method-technical summary').click()
    assert page.locator('.method-formulas').is_visible()
    assert page.locator('#metode .stop-list').is_visible()
    page.locator('.method-technical summary').click()
    assert not page.locator('.method-formulas').is_visible()
    for href in page.locator('a[href^="#"]').evaluate_all('(links) => links.map(a => a.getAttribute("href"))'):
        assert len(href) > 1 and page.locator(f'[id="{href[1:]}"]').count() == 1, href
    assert '−' in page.locator('.method-formulas').text_content()
    assert all('−' not in t for t in page.locator('#rumpun, #cara-kerja, #panduan').all_inner_texts())

    sizes = []
    page.emulate_media(reduced_motion='reduce')
    for width, height in [(1920,1080),(1440,900),(1280,720),(1024,768),(768,1024),(390,844),(360,800)]:
        page.set_viewport_size({'width':width,'height':height})
        page.goto(URL, wait_until='networkidle')
        page.evaluate('document.fonts.ready')
        assert page.evaluate('document.documentElement.scrollWidth <= innerWidth'), width
        page.locator('.method-technical summary').click()
        assert page.locator('.method-formulas').is_visible()
        assert page.evaluate('document.documentElement.scrollWidth <= innerWidth'), ('formula', width)
        page.locator('.method-technical summary').click()
        if width >= 1024:
            hero_bottom = page.locator('.hero').bounding_box()['y'] + page.locator('.hero').bounding_box()['height']
            assert hero_bottom <= height + 1, (width, hero_bottom, height)
        for section in SECTIONS:
            if width < 1024:
                page.locator('[data-menu-open]').click()
                link = page.locator(f'#mobile-menu a[href="#{section}"]')
            else:
                link = page.locator(f'.desktop-nav a[href="#{section}"]')
            link.click()
            page.wait_for_function('(hash) => location.hash === hash', arg='#' + section)
            if width < 1024:
                assert not page.locator('#mobile-menu').evaluate('(d) => d.open')
            page.wait_for_function('''(id) => {
                const target = document.getElementById(id);
                const top = target.getBoundingClientRect().top;
                const offset = parseFloat(getComputedStyle(target).scrollMarginTop) || 0;
                const header = document.querySelector('[data-header]').getBoundingClientRect();
                return Math.abs(top - offset) < 4 && (id === 'beranda' || top >= header.bottom);
            }''', arg=section)
        page.locator('#beranda').scroll_into_view_if_needed()
        page.screenshot(path=str(OUT/f'landing-{width}.png'), full_page=True)
        sizes.append({'width':width,'overflow':False,'allMenuTargetsReached':True})

    page.set_viewport_size({'width':360,'height':800})
    page.goto(URL)
    for closer in ['escape','button','backdrop']:
        page.locator('[data-menu-open]').click()
        for _ in range(10):
            page.keyboard.press('Tab')
            assert page.evaluate("document.activeElement.closest('#mobile-menu') !== null")
        if closer == 'escape':
            page.keyboard.press('Escape')
        elif closer == 'button':
            page.locator('#mobile-menu [data-close]').click()
        else:
            page.mouse.click(4,400)
        assert not page.locator('#mobile-menu').evaluate('(d) => d.open')
        assert page.locator('[data-menu-open]').evaluate('(e) => document.activeElement === e')
    assert page.evaluate('getComputedStyle(document.documentElement).scrollBehavior') == 'auto'
    page.goto(URL)
    page.keyboard.press('Tab')
    assert page.locator('.skip-link').evaluate('(e) => document.activeElement === e')
    page.keyboard.press('Enter')
    assert page.locator('#konten').evaluate('(e) => document.activeElement === e')
    page.set_viewport_size({'width':1280,'height':720})
    for selector in ['.hero__actions a', '.footer-navigation a']:
        for link in page.locator(selector).all():
            target = link.get_attribute('href')
            link.click()
            page.wait_for_function('(hash) => location.hash === hash', arg=target)
    page.set_viewport_size({'width':1440,'height':900})
    for section in ['rumpun','cara-kerja','metode','panduan']:
        element = page.locator('#'+section)
        element.scroll_into_view_if_needed()
        for image in element.locator('img').all():
            image.wait_for(state='visible')
            image.evaluate('(img) => img.decode()')
            assert image.evaluate('(img) => img.naturalWidth > 0')
        element.screenshot(path=str(OUT/f'landing-section-{section}.png'))
    page.locator('footer').screenshot(path=str(OUT/'landing-section-footer.png'))
    assert not errors, errors
    assert not failed_assets, failed_assets
    visual = browser.new_page(bypass_csp=True, reduced_motion='reduce')
    for width,height in [(360,800),(768,1024)]:
        visual.set_viewport_size({'width':width,'height':height})
        visual.goto(URL,wait_until='networkidle')
        for name in ['rumpun','metode','panduan']:
            element = visual.locator('#'+name)
            element.scroll_into_view_if_needed()
            for photo in element.locator('img').all():
                photo.evaluate('(img)=>img.decode()')
            element.screenshot(path=str(OUT/f'landing-section-{name}-{width}.png'), style='.skip-link:not(:focus) { visibility: hidden; }')
    visual.close()
    browser.close()
    no_js = p.chromium.launch()
    static = no_js.new_page(java_script_enabled=False,viewport={'width':360,'height':800})
    static.goto(URL)
    for section in SECTIONS:
        assert static.locator('#'+section).count() == 1
    assert static.locator('noscript').is_visible()
    assert static.locator('[data-tab-panel]:visible').count() == 3
    assert static.locator('[data-journey-panel]:visible').count() == 4
    static.locator('.method-technical summary').click()
    assert static.locator('.method-formulas').is_visible()
    no_js.close()
    result={'viewports':sizes,'consoleErrors':errors,'failedAssets':failed_assets,'states':'PASS: native anchors, mobile focus trap and closing, reduced motion, images, noscript'}
    (OUT/'landing-results.json').write_text(json.dumps(result,indent=2),encoding='utf-8')
    print(json.dumps(result,indent=2))
