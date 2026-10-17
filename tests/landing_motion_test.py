"""Exercise real motion, scroll navigation, replay, and live reduced-motion changes."""
import json
from pathlib import Path
from playwright.sync_api import sync_playwright, expect

OUT = Path(__file__).resolve().parents[1] / 'output' / 'qa'
OUT.mkdir(parents=True, exist_ok=True)
URL = 'http://localhost/web-id3/'

with sync_playwright() as p:
    browser = p.chromium.launch()
    page = browser.new_page(viewport={'width': 1440, 'height': 900}, reduced_motion='no-preference')
    errors = []
    page.on('pageerror', lambda e: errors.append(str(e)))
    page.on('console', lambda m: errors.append(m.text) if m.type == 'error' else None)
    page.goto(URL, wait_until='domcontentloaded')
    assert page.locator('.hero__line > span').first.evaluate('(e) => e.getAnimations().some(a => a.playState === "running")')
    page.wait_for_timeout(1700)
    page.screenshot(path=str(OUT / 'motion-hero-desktop.png'))
    page.evaluate("window.scrollTo({top: document.querySelector('#rumpun').offsetTop - 96, behavior: 'instant'})")
    page.wait_for_timeout(120)
    assert page.locator('.explore-stage').evaluate('(e) => e.getAnimations().some(a => a.playState === "running")')
    expect(page.locator('.desktop-nav .nav-link--active')).to_have_attribute('href', '#rumpun')
    assert page.locator('[data-header]').evaluate('(e) => getComputedStyle(e).position') == 'fixed'
    progress = page.locator('[data-reading-progress]').evaluate('(e) => parseFloat(e.style.getPropertyValue("--reading-progress"))')
    assert 0 < progress < 1
    first_drift = page.locator('#rumpun [data-parallax]').evaluate('(e) => e.style.getPropertyValue("--image-drift")')
    page.evaluate("window.scrollBy({top: 140, behavior: 'instant'})")
    page.wait_for_timeout(60)
    assert first_drift != page.locator('#rumpun [data-parallax]').evaluate('(e) => e.style.getPropertyValue("--image-drift")')
    page.evaluate("window.scrollTo({top: document.querySelector('#cara-kerja').offsetTop + 150, behavior: 'instant'})")
    page.wait_for_timeout(80)
    assert page.locator('.journey-workspace').evaluate('(e) => e.getAnimations().some(a => a.playState === "running")')
    expect(page.locator('.desktop-nav .nav-link--active')).to_have_attribute('href', '#cara-kerja')
    page.locator('.method-tree').scroll_into_view_if_needed()
    page.wait_for_timeout(100)
    assert page.locator('.tree-paths path').first.evaluate('(e) => e.getAnimations().some(a => a.playState === "running")')
    page.wait_for_function("() => !document.querySelector('[data-tree-replay]').disabled")
    page.locator('[data-tree-replay]').click()
    assert page.locator('[data-tree-replay]').is_disabled()
    assert page.locator('.tree-paths path').first.evaluate('(e) => e.getAnimations().some(a => a.playState === "running")')
    page.wait_for_timeout(450)
    page.screenshot(path=str(OUT / 'motion-tree-during.png'))
    page.emulate_media(reduced_motion='reduce')
    page.wait_for_timeout(80)
    assert page.locator('[data-tree-replay]').is_hidden()
    assert page.locator('.tree-node').evaluate_all('(nodes) => nodes.every(e => getComputedStyle(e).opacity === "1")')
    assert page.evaluate('document.getAnimations().filter(a => a.playState === "running").length') == 0
    assert page.locator('[data-parallax]').evaluate_all('(photos) => photos.every(e => getComputedStyle(e).transform === "none")')
    page.emulate_media(reduced_motion='no-preference')
    expect(page.locator('[data-tree-replay]')).to_be_visible()
    page.locator('[data-tree-replay]').click()
    page.wait_for_function("() => !document.querySelector('[data-tree-replay]').disabled")
    page.locator('.method-tree').screenshot(path=str(OUT / 'motion-tree-complete.png'))
    page.set_viewport_size({'width': 390, 'height': 844})
    page.goto(URL, wait_until='networkidle')
    page.locator('[data-menu-open]').click()
    assert page.locator('#mobile-menu').evaluate('(e) => e.getAnimations().some(a => a.playState === "running")')
    expect(page.locator('[data-menu-open]')).to_have_attribute('aria-expanded', 'true')
    page.locator('#mobile-menu a[href="#panduan"]').click()
    expect(page.locator('[data-menu-open]')).to_have_attribute('aria-expanded', 'false')
    page.wait_for_function("() => Math.abs(document.querySelector('#panduan').getBoundingClientRect().top - 88) < 3")
    expect(page.locator('#mobile-menu a[href="#panduan"]')).to_have_attribute('aria-current', 'location')
    assert page.evaluate('document.documentElement.scrollWidth <= innerWidth')
    page.wait_for_timeout(1400)
    page.screenshot(path=str(OUT / 'motion-guide-mobile.png'))
    assert not errors, errors
    result = {'status': 'PASS', 'heroEntrance': True, 'scrollReveals': True, 'parallax': True,
              'scrollSpy': True, 'stickyHeader': True, 'readingProgress': True, 'treeDrawAndReplay': True,
              'liveReducedMotion': True, 'mobileMenuMotion': True, 'consoleErrors': errors}
    (OUT / 'landing-motion-results.json').write_text(json.dumps(result, indent=2), encoding='utf-8')
    print(json.dumps(result, indent=2))
    browser.close()
