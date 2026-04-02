<?php ob_start(); ?>
<?php if (!empty($errors ?? [])): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<?php
$isEdit = isset($product);
$data = $product ?? ($old ?? []);
?>

<div class="card">
  <div class="card-body">
    <form method="post" action="<?= BASE_URL ?>admin/products/<?= $isEdit ? 'update' : 'store' ?>">
      <?php if ($isEdit): ?>
        <input type="hidden" name="product_id" value="<?= (int) $data['product_id'] ?>">
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label">Tên sản phẩm</label>
        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($data['name'] ?? '') ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Danh mục</label>
        <select name="category_id" class="form-select" required>
          <option value="">-- Chọn danh mục --</option>
          <?php foreach (($categories ?? []) as $c): ?>
            <option value="<?= (int) $c['category_id'] ?>" <?= isset($data['category_id']) && (int) $data['category_id'] === (int) $c['category_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Giá (đ)</label>
          <input type="number" step="1000" min="0" name="price" class="form-control" required value="<?= htmlspecialchars($data['price'] ?? '') ?>">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Tồn kho</label>
          <input type="number" min="0" name="stock" class="form-control" required value="<?= htmlspecialchars($data['stock'] ?? 0) ?>">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Đơn vị</label>
          <input type="text" name="unit" class="form-control" value="<?= htmlspecialchars($data['unit'] ?? 'kg') ?>">
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($data['description'] ?? '') ?></textarea>
      </div>
      <?php if ($isEdit): ?>
        <div class="mb-3">
          <label class="form-label">Trạng thái</label>
          <select name="status" class="form-select">
            <?php $status = $data['status'] ?? 'active'; ?>
            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Đang bán</option>
            <option value="out_of_stock" <?= $status === 'out_of_stock' ? 'selected' : '' ?>>Hết hàng</option>
            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Ẩn</option>
          </select>
        </div>
      <?php endif; ?>

      <button class="btn btn-success"><?= $isEdit ? 'Cập nhật' : 'Thêm mới' ?></button>
      <a href="<?= BASE_URL ?>admin/products" class="btn btn-secondary">Quay lại</a>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean();
view('layouts.AdminLayout', [
  'title' => $title ?? ($isEdit ? 'Cập nhật sản phẩm' : 'Thêm sản phẩm'),
  'pageTitle' => $title ?? ($isEdit ? 'Cập nhật sản phẩm' : 'Thêm sản phẩm'),
  'content' => $content,
]);
?>
