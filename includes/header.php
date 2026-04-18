<!doctype html>
<html lang="en">

<head>
  <?php $assetBasePath = isset($assetBasePath) ? rtrim((string)$assetBasePath, '/') : './assets'; ?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($page_name) ? $page_name : 'Locator\'s Slip' ?></title>
  <link rel="shortcut icon" type="image/png" href="<?= htmlspecialchars($assetBasePath) ?>/images/logos/favicon.png" />
  <link rel="stylesheet" href="<?= htmlspecialchars($assetBasePath) ?>/css/styles.min.css" />
  <link rel="stylesheet" href="<?= htmlspecialchars($assetBasePath) ?>/css/custom.css">
  <link rel="stylesheet" href="<?= htmlspecialchars($assetBasePath) ?>/css/custom-cols.css" />
  <?php if (isset($pageHeadExtra) && is_string($pageHeadExtra) && trim($pageHeadExtra) !== ''): ?>
      <?= $pageHeadExtra ?>
  <?php endif; ?>
</head>

<body>