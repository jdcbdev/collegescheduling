  <?php $assetBasePath = isset($assetBasePath) ? rtrim((string)$assetBasePath, '/') : './assets'; ?>
  <script src="<?= htmlspecialchars($assetBasePath) ?>/libs/jquery/dist/jquery.min.js"></script>
  <script src="<?= htmlspecialchars($assetBasePath) ?>/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= htmlspecialchars($assetBasePath) ?>/js/sidebarmenu.js"></script>
  <script src="<?= htmlspecialchars($assetBasePath) ?>/js/app.min.js"></script>
  <script src="<?= htmlspecialchars($assetBasePath) ?>/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="<?= htmlspecialchars($assetBasePath) ?>/libs/simplebar/dist/simplebar.js"></script>
  <script src="<?= htmlspecialchars($assetBasePath) ?>/js/dashboard.js"></script>
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

  <?php if (isset($pageInlineScript) && is_string($pageInlineScript) && trim($pageInlineScript) !== ''): ?>
  <script>
      <?= $pageInlineScript ?>
  </script>
  <?php endif; ?>

</body>

</html>