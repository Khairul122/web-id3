<?php
// Dialog flash (berhasil/gagal) dan template konfirmasi. $flash boleh diisi langsung oleh halaman.
$flash ??= take_flash();
$flashIcons = ['success' => 'check', 'error' => 'alert'];
?>
<?php if ($flash): ?>
<dialog class="alert-dialog alert-dialog--<?= e($flash['type']) ?>" data-flash open aria-labelledby="flash-title" aria-describedby="flash-message">
  <div class="alert-dialog__icon"><?= icon($flashIcons[$flash['type']] ?? 'alert') ?></div>
  <h2 class="alert-dialog__title" id="flash-title"><?= e($flash['title']) ?></h2>
  <?php if ($flash['message'] !== ''): ?><p class="alert-dialog__message" id="flash-message"><?= e($flash['message']) ?></p><?php endif; ?>
  <form method="dialog" class="alert-dialog__actions"><button class="alert-dialog__ok" type="submit" autofocus>OK</button></form>
</dialog>
<?php endif; ?>
<dialog class="alert-dialog alert-dialog--confirm" id="confirm-dialog" aria-labelledby="confirm-title" aria-describedby="confirm-message">
  <div class="alert-dialog__icon"><?= icon('question') ?></div>
  <h2 class="alert-dialog__title" id="confirm-title">Konfirmasi</h2>
  <p class="alert-dialog__message" id="confirm-message"></p>
  <form method="dialog" class="alert-dialog__actions">
    <button class="alert-dialog__cancel" type="submit" value="cancel">Batal</button>
    <button class="alert-dialog__ok" type="submit" value="ok" data-confirm-ok>Ya, lanjutkan</button>
  </form>
</dialog>
