<?php ob_start(); ?>
<?php if ($msg = getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>
<?php if ($msg = getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="d-flex justify-content-between mb-3">
  <h3>Quản lý sản phẩm</h3>
  <a href="<?= BASE_URL ?>admin/products/create" class="btn btn-primary">Thêm sản phẩm</a>
</div>

<div class="card">
  <div class="card-body p-0">
    <table class="table mb-0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Tên sản phẩm</th>
          <th>Danh mục</th>
          <th>Giá</th>
          <th>Tồn kho</th>
          <th>Trạng thái</th>
          <th style="width: 140px;"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (($products ?? []) as $p): ?>
          <tr>
            <td><?= (int) $p['product_id'] ?></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= htmlspecialchars($p['category_name'] ?? '') ?></td>
            <td><?= number_format((float) $p['price']) ?> đ</td>
            <td><?= (int) $p['stock'] . ' ' . htmlspecialchars($p['unit']) ?></td>
            <td><?= htmlspecialchars($p['status']) ?></td>
            <td class="text-end">
              <a href="<?= BASE_URL ?>admin/products/edit&id=<?= (int) $p['product_id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
              <form action="<?= BASE_URL ?>admin/products/delete" method="post" class="d-inline" onsubmit="return confirm('Xóa sản phẩm này?');">
                <input type="hidden" name="product_id" value="<?= (int) $p['product_id'] ?>">
                <button class="btn btn-sm btn-danger">Xóa</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$content = ob_get_clean();
view('layouts.AdminLayout', [
  'title' => 'Quản lý sản phẩm',
  'pageTitle' => 'Quản lý sản phẩm',
  'content' => $content,
]);
?>
