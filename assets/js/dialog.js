'use strict';

// Dialog flash: dirender server dengan atribut open agar tetap terbaca tanpa JS, lalu dijadikan modal.
const flash = document.querySelector('[data-flash]');
if (flash && typeof flash.showModal === 'function') {
  flash.close();
  flash.showModal();
}

// Konfirmasi: <form data-confirm="Pesan" data-confirm-title="Judul" data-confirm-ok="Label" data-confirm-danger>
const confirmDialog = document.getElementById('confirm-dialog');
if (confirmDialog && typeof confirmDialog.showModal === 'function') {
  const title = confirmDialog.querySelector('#confirm-title');
  const message = confirmDialog.querySelector('#confirm-message');
  const ok = confirmDialog.querySelector('[data-confirm-ok]');
  let pending = null;

  document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.dataset.confirm || form.dataset.confirmed === '1') return;
    event.preventDefault();
    pending = form;
    title.textContent = form.dataset.confirmTitle || 'Konfirmasi';
    message.textContent = form.dataset.confirm;
    ok.textContent = form.dataset.confirmOk || 'Ya, lanjutkan';
    ok.classList.toggle('alert-dialog__ok--danger', 'confirmDanger' in form.dataset);
    confirmDialog.returnValue = '';
    confirmDialog.showModal();
    ok.focus();
  });

  confirmDialog.addEventListener('close', () => {
    if (pending && confirmDialog.returnValue === 'ok') {
      pending.dataset.confirmed = '1';
      pending.requestSubmit();
    }
    pending = null;
  });
}

// Klik di luar kotak dialog menutup dialog (sama dengan Batal / OK).
document.querySelectorAll('.alert-dialog').forEach((dialog) => {
  dialog.addEventListener('click', (event) => {
    if (event.target !== dialog) return;
    const rect = dialog.getBoundingClientRect();
    const outside = event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom;
    if (outside) dialog.close('cancel');
  });
});

// Status memproses pada form yang dikirim.
document.addEventListener('submit', (event) => {
  const form = event.target;
  if (!(form instanceof HTMLFormElement) || event.defaultPrevented || form.method === 'dialog' || !form.hasAttribute('data-busy')) return;
  const button = form.querySelector('[type="submit"]');
  if (!button) return;
  button.disabled = true;
  button.dataset.label = button.innerHTML;
  button.textContent = 'Memproses...';
});
window.addEventListener('pageshow', () => {
  document.querySelectorAll('[data-busy] [type="submit"][data-label]').forEach((button) => {
    button.disabled = false;
    button.innerHTML = button.dataset.label;
  });
});
