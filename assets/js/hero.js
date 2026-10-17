'use strict';

const menu = document.querySelector('#mobile-menu');
const menuTrigger = document.querySelector('[data-menu-open]');

if (menu && menuTrigger) {
  menuTrigger.hidden = false;
  menuTrigger.setAttribute('aria-expanded', 'false');
  menuTrigger.addEventListener('click', () => {
    menu.showModal();
    menuTrigger.setAttribute('aria-expanded', 'true');
  });
  menu.addEventListener('close', () => {
    menuTrigger.setAttribute('aria-expanded', 'false');
    menuTrigger.focus({ preventScroll: true });
  });
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
    const last = controls[controls.length - 1];
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
function updateCurrentSection(current) {
  sectionLinks.forEach((link) => {
    const active = link.getAttribute('href') === current;
    link.classList.toggle('nav-link--active', active && link.classList.contains('nav-link'));
    if (active) link.setAttribute('aria-current', 'location');
    else link.removeAttribute('aria-current');
  });
}

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
const activeAnimations = new Set();
const ease = 'cubic-bezier(.22, 1, .36, 1)';

function animate(element, keyframes, options = {}) {
  if (reducedMotion.matches || !element?.animate) return null;
  const animation = element.animate(keyframes, { duration: 800, easing: ease, fill: 'backwards', ...options });
  activeAnimations.add(animation);
  animation.finished.catch(() => {}).finally(() => activeAnimations.delete(animation));
  return animation;
}

function reveal(element, variant = 'rise', delay = 0) {
  const transforms = { rise: 'translateY(36px)', side: 'translateX(36px)', photo: 'translateY(28px) scale(.96)' };
  return animate(element, [
    { opacity: 0, transform: transforms[variant] },
    { opacity: 1, transform: 'none' },
  ], { delay, duration: variant === 'photo' ? 1100 : 800 });
}

document.querySelectorAll('.hero__line > span').forEach((line, index) => {
  animate(line, [{ transform: 'translateY(110%)' }, { transform: 'translateY(0)' }], { duration: 1100, delay: 120 + index * 140 });
});
reveal(document.querySelector('.hero__eyebrow'), 'side');
reveal(document.querySelector('.hero__description'), 'rise', 420);
reveal(document.querySelector('.hero__actions'), 'rise', 560);

const revealTargets = new Map();
document.querySelectorAll('.landing-section .section-eyebrow, .section-title, .section-intro, .process-note, .guide-note, .footer-invitation').forEach((element) => {
  revealTargets.set(element, ['rise', 0]);
});
document.querySelectorAll('.editorial-photo, .teacher-guide__content').forEach((element) => revealTargets.set(element, ['photo', 80]));
['.foundation__item', '.rumpun-definitions > div', '.process-list > li', '.student-steps li', '.footer-groups > div'].forEach((selector) => {
  document.querySelectorAll(selector).forEach((element, index) => revealTargets.set(element, [selector.includes('definitions') ? 'side' : 'rise', index * 100]));
});

const tree = document.querySelector('.method-tree');
const replay = document.querySelector('[data-tree-replay]');
let treePlaying = false;
async function playTree() {
  if (!tree || !replay || reducedMotion.matches || treePlaying) return;
  treePlaying = true;
  replay.disabled = true;
  replay.setAttribute('aria-busy', 'true');
  tree.setAttribute('aria-busy', 'true');
  try {
    const animations = [];
    tree.querySelectorAll('.tree-paths path').forEach((path, index) => {
      animations.push(animate(path, [{ strokeDasharray: '1', strokeDashoffset: '1' }, { strokeDasharray: '1', strokeDashoffset: '0' }], {
        duration: 700, delay: index < 2 ? 250 : 900, easing: 'ease-in-out',
      }));
    });
    tree.querySelectorAll('.tree-node').forEach((node, index) => {
      animations.push(animate(node, [{ opacity: 0 }, { opacity: 1 }], { duration: 400, delay: index === 0 ? 0 : index < 3 ? 650 : 1300 }));
    });
      animations.push(animate(tree.querySelector('.tree-labels'), [{ opacity: 0 }, { opacity: 1 }], { duration: 400, delay: 650 }));
    await Promise.allSettled(animations.filter(Boolean).map((animation) => animation.finished));
  } finally {
    treePlaying = false;
    replay.disabled = false;
    replay.removeAttribute('aria-busy');
    tree.removeAttribute('aria-busy');
  }
}
if (replay) {
  replay.hidden = reducedMotion.matches || !Element.prototype.animate;
  replay.addEventListener('click', playTree);
}

// Content stays visible by default, including when JavaScript or observers are unavailable.
if ('IntersectionObserver' in window) {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      observer.unobserve(entry.target);
      if (entry.target === tree) playTree();
      else reveal(entry.target, ...revealTargets.get(entry.target));
    });
  }, { threshold: .12, rootMargin: '0px 0px -24px 0px' });
  revealTargets.forEach((_, element) => observer.observe(element));
  if (tree) observer.observe(tree);
}

const header = document.querySelector('[data-header]');
const progress = document.querySelector('[data-reading-progress]');
const sections = [...document.querySelectorAll('main > section[id]')];
const photos = [...document.querySelectorAll('[data-parallax]')];
let framePending = false;
let currentSection = '';

function renderScroll() {
  framePending = false;
  const scrolled = window.scrollY > 72;
  header?.classList.toggle('is-scrolled', scrolled);
  const scrollRange = document.documentElement.scrollHeight - innerHeight;
  progress?.style.setProperty('--reading-progress', String(scrollRange > 0 ? Math.min(1, scrollY / scrollRange) : 0));

  let current = '#beranda';
  const readingLine = (header?.offsetHeight || 80) + innerHeight * .2;
  sections.forEach((section) => {
    if (section.getBoundingClientRect().top <= readingLine) current = '#' + section.id;
  });
  if (current !== currentSection) {
    currentSection = current;
    updateCurrentSection(current);
  }

  if (reducedMotion.matches) return;
  photos.forEach((photo) => {
    const surface = photo.closest('.hero__visual, .photo-window');
    const rect = surface.getBoundingClientRect();
    if (rect.bottom < 0 || rect.top > innerHeight) return;
    const amount = photo.dataset.parallax === 'hero' ? Math.min(scrollY * .16, 64) : Math.max(-12, Math.min(12, (innerHeight / 2 - rect.top - rect.height / 2) * .035));
    photo.style.setProperty('--image-drift', `${amount.toFixed(2)}px`);
  });
}
function scheduleScroll() {
  if (framePending) return;
  framePending = true;
  requestAnimationFrame(renderScroll);
}
window.addEventListener('scroll', scheduleScroll, { passive: true });
window.addEventListener('resize', scheduleScroll);
window.addEventListener('hashchange', scheduleScroll);
reducedMotion.addEventListener('change', () => {
  if (reducedMotion.matches) {
    activeAnimations.forEach((animation) => animation.cancel());
    photos.forEach((photo) => photo.style.removeProperty('--image-drift'));
  }
  if (replay) replay.hidden = reducedMotion.matches || !Element.prototype.animate;
  scheduleScroll();
});
renderScroll();
