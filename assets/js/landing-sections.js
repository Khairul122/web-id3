'use strict';

const landingMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
const sectionAnimations = new Set();
function enterSection(element, distance = 12, duration = 320) {
  if (landingMotion.matches || !element?.animate) return;
  const animation = element.animate([
    { opacity: 0, transform: `translateY(${distance}px)` },
    { opacity: 1, transform: 'none' },
  ], { duration, easing: 'cubic-bezier(.22, 1, .36, 1)' });
  sectionAnimations.add(animation);
  animation.finished.catch(() => {}).finally(() => sectionAnimations.delete(animation));
}
landingMotion.addEventListener('change', () => {
  if (landingMotion.matches) sectionAnimations.forEach((animation) => animation.cancel());
});

document.querySelectorAll('[data-tabs]').forEach((explorer) => {
  const list = explorer.querySelector('[data-tab-list]');
  const tabs = [...explorer.querySelectorAll('[data-tab]')];
  const panels = [...explorer.querySelectorAll('[data-tab-panel]')];
  function selectTab(tab) {
    tabs.forEach((item) => {
      const selected = item === tab;
      item.setAttribute('aria-selected', String(selected));
      item.tabIndex = selected ? 0 : -1;
      document.getElementById(item.dataset.tab).hidden = !selected;
    });
  }
  list.setAttribute('role', 'tablist');
  tabs.forEach((tab, index) => {
    tab.setAttribute('role', 'tab');
    tab.setAttribute('aria-controls', tab.dataset.tab);
    const panel = panels[index];
    panel.setAttribute('role', 'tabpanel');
    panel.setAttribute('aria-labelledby', tab.id);
    panel.tabIndex = 0;
    tab.addEventListener('click', () => {
      if (tab.getAttribute('aria-selected') === 'true') return;
      selectTab(tab);
      enterSection(panel);
    });
    tab.addEventListener('keydown', (event) => {
      let next;
      if (event.key === 'ArrowRight') next = (index + 1) % tabs.length;
      if (event.key === 'ArrowLeft') next = (index - 1 + tabs.length) % tabs.length;
      if (event.key === 'Home') next = 0;
      if (event.key === 'End') next = tabs.length - 1;
      if (next === undefined) return;
      event.preventDefault();
      selectTab(tabs[next]);
      enterSection(panels[next]);
      tabs[next].focus();
    });
  });
  selectTab(tabs[0]);
  list.hidden = false;
});

document.querySelectorAll('[data-journey]').forEach((journey) => {
  const steps = [...journey.querySelectorAll('[data-journey-step]')];
  const panels = [...journey.querySelectorAll('[data-journey-panel]')];
  steps.forEach((step) => step.setAttribute('aria-controls', step.hash.slice(1)));
  function selectStep(step) {
    const target = step.getAttribute('href').slice(1);
    steps.forEach((item) => {
      if (item === step) item.setAttribute('aria-current', 'step');
      else item.removeAttribute('aria-current');
    });
    panels.forEach((panel) => { panel.hidden = panel.id !== target; });
  }
  steps.forEach((step) => step.addEventListener('click', (event) => {
    event.preventDefault();
    if (step.getAttribute('aria-current') === 'step') return;
    selectStep(step);
    enterSection(document.getElementById(step.hash.slice(1)));
  }));
  selectStep(steps.find((step) => step.hash === location.hash) || steps[0]);
  window.addEventListener('hashchange', () => {
    const step = steps.find((item) => item.hash === location.hash);
    if (step) selectStep(step);
  });
});

document.querySelectorAll('[data-checklist]').forEach((checklist) => {
  const inputs = [...checklist.querySelectorAll('input[type="checkbox"]')];
  const progress = checklist.parentElement.querySelector('[data-checklist-progress]');
  const meter = progress.querySelector('progress');
  const status = progress.querySelector('[data-checklist-status]');
  function updateProgress() {
    const completed = inputs.filter((input) => input.checked).length;
    meter.value = completed;
    status.textContent = completed === inputs.length
      ? 'Semua siap. Saatnya berdiskusi dengan guru BK.'
      : `${completed} dari ${inputs.length} persiapan selesai`;
  }
  inputs.forEach((input) => input.addEventListener('change', updateProgress));
  updateProgress();
  progress.hidden = false;
});

if ('IntersectionObserver' in window) {
  const sectionObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      sectionObserver.unobserve(entry.target);
      enterSection(entry.target, 24, 650);
    });
  }, { threshold: .1 });
  document.querySelectorAll('.explore-stage, .journey-workspace, .preparation-photo, .closing-invitation')
    .forEach((element) => sectionObserver.observe(element));
}
