'use strict';

// Sidebar ponsel.
const toggle = document.querySelector('[data-sidebar-toggle]');
const sidebar = document.getElementById('sidebar');
const setSidebar = (open) => {
  document.body.classList.toggle('sidebar-open', open);
  toggle?.setAttribute('aria-expanded', String(open));
  if (open) sidebar.querySelector('a')?.focus();
};
toggle?.addEventListener('click', () => setSidebar(!document.body.classList.contains('sidebar-open')));
document.querySelector('[data-sidebar-close]')?.addEventListener('click', () => setSidebar(false));
document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape' && document.body.classList.contains('sidebar-open')) {
    setSidebar(false);
    toggle?.focus();
  }
});

// Kategori nilai langsung saat diketik: <form data-tinggi="95" data-sedang="92"> + <input data-score="id-hint">.
document.querySelectorAll('form[data-tinggi]').forEach((form) => {
  const tinggi = Number(form.dataset.tinggi);
  const sedang = Number(form.dataset.sedang);
  form.querySelectorAll('[data-score]').forEach((input) => {
    const hint = document.getElementById(input.dataset.score);
    const update = () => {
      if (!hint) return;
      const raw = input.value.trim();
      const value = Number(raw);
      if (raw === '') hint.textContent = 'Kosong: kategori dipilih manual.';
      else if (Number.isNaN(value) || value < 0 || value > 100) hint.textContent = 'Isi angka 0 sampai 100.';
      else hint.textContent = 'Kategori: ' + (value >= tinggi ? 'Tinggi' : value >= sedang ? 'Sedang' : 'Rendah');
    };
    input.addEventListener('input', update);
    update();
  });
});

// Tombol cetak tanpa skrip inline.
document.querySelectorAll('[data-print]').forEach((button) => button.addEventListener('click', () => window.print()));
