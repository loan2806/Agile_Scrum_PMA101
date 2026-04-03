<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <title><?= $title ?? 'Dashboard' ?></title>

  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- CSS -->
  <link rel="stylesheet" href="<?= asset('dist/css/adminlte.css') ?>" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">


  <!-- MAIN -->
  <main class="app-main">

    <!-- TITLE -->
    <div class="app-content-header">
      <div class="container-fluid">
        <h3><?= $pageTitle ?? '' ?></h3>
      </div>
    </div>

    <!-- CONTENT -->
    <div class="app-content">
      <div class="container-fluid">
        <?= $content ?? '' ?>
      </div>
    </div>

  </main>

  <!-- FOOTER -->

</div>

<!-- JS -->
<script src="<?= asset('dist/js/adminlte.js') ?>"></script>

</body>
</html>