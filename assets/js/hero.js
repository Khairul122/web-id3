'use strict';

const menu = document.querySelector('#mobile-menu');
const menuTrigger = document.querySelector('[data-menu-open]');

if (menu && menuTrigger) {
  menuTrigger.hidden = false;
  menuTrigger.addEventListener('click', () => menu.showModal());
  menu.querySelector('[data-close]').addEventListener('click', () => menu.close());
  menu.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
      const href = link.getAttribute('href');
      menu.close();
      if (href.startsWith('#')) {
        const target = document.getElementById(href.slice(1));
        requestAnimationFrame(() => target?.focus({ preventScroll: true }));
      }
    });
  });
  menu.addEventListener('keydown', (event) => {
    if (event.key !== 'Tab') return;
    const controls = [...menu.querySelectorAll('a[href], button:not([disabled])')]
      .filter((control) => control.getClientRects().length);
    const first = controls[0];
    const last = controls.at(-1);
    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  });
  menu.addEventListener('click', (event) => {
    const rect = menu.getBoundingClientRect();
    if (event.target === menu && (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom)) {
      menu.close();
    }
  });
}

const sectionLinks = document.querySelectorAll('.desktop-nav a[href^="#"], #mobile-menu a[href^="#"]');
function updateCurrentSection() {
  const current = location.hash || '#beranda';
  sectionLinks.forEach((link) => {
    const active = link.getAttribute('href') === current;
    link.classList.toggle('nav-link--active', active && link.classList.contains('nav-link'));
    if (active) link.setAttribute('aria-current', 'location');
    else link.removeAttribute('aria-current');
  });
}
window.addEventListener('hashchange', updateCurrentSection);
updateCurrentSection();
