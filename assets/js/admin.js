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
const roleSelect = document.querySelector('#role');
const kodeSiswaField = document.querySelector('[data-kode-siswa]');
if (roleSelect && kodeSiswaField) {
  const updateRole = () => { kodeSiswaField.hidden = roleSelect.value !== 'siswa'; };
  roleSelect.addEventListener('change', updateRole);
  updateRole();
}

document.querySelectorAll('[data-print]').forEach((button) => button.addEventListener('click', () => window.print()));

// Unduh PNG dari gambar SVG yang sama origin: digambar ke canvas dua kali ukuran asli.
document.querySelectorAll('[data-svg-png]').forEach((button) => {
  button.hidden = false;
  button.addEventListener('click', () => {
    const label = button.innerHTML;
    button.disabled = true;
    button.textContent = 'Memproses...';
    const image = new Image();
    image.onload = () => {
      const scale = image.naturalWidth * 2 > 16000 ? 1 : 2;
      const canvas = document.createElement('canvas');
      canvas.width = image.naturalWidth * scale;
      canvas.height = image.naturalHeight * scale;
      const context = canvas.getContext('2d');
      context.scale(scale, scale);
      context.drawImage(image, 0, 0);
      canvas.toBlob((blob) => {
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = button.dataset.filename;
        link.click();
        setTimeout(() => URL.revokeObjectURL(link.href), 1000);
        button.disabled = false;
        button.innerHTML = label;
      }, 'image/png');
    };
    image.onerror = () => {
      button.disabled = false;
      button.innerHTML = label;
      button.insertAdjacentText('afterend', ' Gambar gagal dimuat.');
    };
    image.src = button.dataset.svgPng;
  });
});

// Gambar pohon: klik untuk beralih antara pas lebar dan ukuran asli.
document.querySelectorAll('[data-zoom] img').forEach((image) => {
  image.addEventListener('click', () => image.parentElement.classList.toggle('is-zoomed'));
});
