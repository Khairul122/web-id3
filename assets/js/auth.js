'use strict';

document.querySelectorAll('[data-password-toggle]').forEach((button) => {
  const field = document.getElementById(button.dataset.passwordToggle);
  if (!field) return;
  button.hidden = false;
  const label = button.getAttribute('aria-label');
  button.addEventListener('click', () => {
    const visible = field.type === 'password';
    field.type = visible ? 'text' : 'password';
    button.setAttribute('aria-pressed', String(visible));
    button.setAttribute('aria-label', visible ? label.replace('Tampilkan', 'Sembunyikan') : label);
  });
});

document.querySelectorAll('[data-auth-form]').forEach((form) => {
  const button = form.querySelector('[data-submit]');
  const progress = form.querySelector('.auth-form__progress span');
  const fields = [...form.querySelectorAll('input:not([type="hidden"])')];
  const updateProgress = () => {
    if (progress) progress.style.width = `${(fields.filter((field) => field.value.trim()).length / fields.length) * 100}%`;
  };
  fields.forEach((field) => field.addEventListener('input', updateProgress));
  updateProgress();
  const status = form.querySelector('[data-submit-status]');
  form.addEventListener('submit', () => {
    button.disabled = true;
    form.setAttribute('aria-busy', 'true');
    status.textContent = 'Memproses permintaan Anda...';
  });
  window.addEventListener('pageshow', () => {
    button.disabled = false;
    form.removeAttribute('aria-busy');
    status.textContent = '';
  });
});

document.querySelector('[data-form-alert]')?.focus();

const help = document.getElementById('login-help');
const helpTrigger = document.querySelector('[data-help-open]');
if (help && helpTrigger) {
  helpTrigger.hidden = false;
  helpTrigger.addEventListener('click', () => help.showModal());
  help.querySelectorAll('[data-help-close]').forEach((button) => {
    button.addEventListener('click', () => help.close());
  });
  help.addEventListener('keydown', (event) => {
    if (event.key !== 'Tab') return;
    const controls = [...help.querySelectorAll('button')];
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
  help.addEventListener('click', (event) => {
    const rect = help.getBoundingClientRect();
    if (event.target === help && (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom)) help.close();
  });
}
