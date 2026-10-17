'use strict';

const menu = document.querySelector('#mobile-menu');
const info = document.querySelector('#info-dialog');
const content = document.querySelector('#dialog-content');
const menuTrigger = document.querySelector('[data-menu-open]');
let returnFocus = null;

document.querySelectorAll('[data-dialog]').forEach((button) => {
  button.addEventListener('click', () => {
    const template = document.getElementById(`content-${button.dataset.dialog}`);
    if (!template) return;
    returnFocus = menu.open ? menuTrigger : button;
    if (menu.open) menu.close();
    content.replaceChildren(template.content.cloneNode(true));
    info.showModal();
  });
});

menuTrigger.addEventListener('click', () => menu.showModal());

document.querySelectorAll('[data-close]').forEach((button) => {
  button.addEventListener('click', () => button.closest('dialog').close());
});

[menu, info].forEach((dialog) => {
  dialog.addEventListener('keydown', (event) => {
    if (event.key !== 'Tab') return;
    const controls = [...dialog.querySelectorAll('a[href], button:not([disabled])')]
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
  dialog.addEventListener('click', (event) => {
    const rect = dialog.getBoundingClientRect();
    if (event.target === dialog && (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom)) {
      dialog.close();
    }
  });
});

info.addEventListener('close', () => {
  if (returnFocus) returnFocus.focus();
});
