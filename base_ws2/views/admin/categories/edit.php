
<div class="container-fluid py-3 px-4">

  <div class="card border-0 shadow-sm p-3">
    <h6>✏️ Chỉnh sửa danh mục</h6>

    <?php if ($msg = getFlash('error')): ?>
      <div class="alert alert-danger py-2 px-3 mb-2 small"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>admin/categories/update">
      <input type="hidden" name="id" value="<?= (int)$category['category_id'] ?>">
      <div class="input-group input-group-sm">
        <span class="input-group-text">✏️</span>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($category['name']) ?>">
        <button class="btn btn-success px-3">Lưu</button>
        <a href="<?= BASE_URL ?>admin/categories" class="btn btn-secondary px-3">Hủy</a>
      </div>
    </form>
  </div>

</div>

<?php
$content = ob_get_clean();
view('layouts.AdminLayout', [
  'title' => 'Chỉnh sửa danh mục',
  'content' => $content
]);
?>