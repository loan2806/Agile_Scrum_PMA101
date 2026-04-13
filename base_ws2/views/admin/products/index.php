<?php ob_start(); ?>

<div class="container-fluid py-3 px-4">

  <!-- Alert -->
  <?php if ($msg = getFlash('success')): ?>
    <div class="alert alert-success py-2 px-3 mb-2 small">
      <?= htmlspecialchars($msg) ?>
    </div>
  <?php endif; ?>

  <?php if ($msg = getFlash('error')): ?>
    <div class="alert alert-danger py-2 px-3 mb-2 small">
      <?= htmlspecialchars($msg) ?>
    </div>
  <?php endif; ?>

  <!-- Top bar -->
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
      <div class="fw-bold">📦 Sản phẩm</div>
      <div class="d-flex gap-2">
        <form method="get" class="d-flex gap-2">
          <input type="text" name="q" class="form-control form-control-sm" placeholder="Tìm sản phẩm..." style="width:220px" value="<?= htmlspecialchars($keyword ?? '') ?>">
          <select name="category" class="form-select form-select-sm" style="width:150px">
            <option value="0">Tất cả danh mục</option>
            <?php foreach (($categories ?? []) as $c): ?>
              <option value="<?= (int)$c['category_id'] ?>" <?= (isset($categoryId) && (int)$categoryId === (int)$c['category_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <button class="btn btn-sm btn-primary">Tìm</button>
        </form>
        <a href="<?= BASE_URL ?>admin/products/create" class="btn btn-sm btn-success">+ Thêm mới</a>
      </div>
    </div>
  </div>

  <!-- Table -->
  <div class="card border-0 shadow-sm">
    <div class="table-responsive">

      <table class="table table-hover align-middle mb-0 small">

        <thead class="table-light">
          <tr class="text-muted">
            <th width="50">ID</th>
            <th width="80">Ảnh</th>
            <th>Tên sản phẩm</th>
            <th width="160">Danh mục</th>
            <th width="140">Giá</th>
            <th width="100">Kho</th>
            <th width="140">Trạng thái</th>
            <th width="120" class="text-end">Thao tác</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach (($products ?? []) as $p): ?>
            <tr>
              <td class="fw-bold">#<?= (int)$p['product_id'] ?></td>

              <td>
                <?php if (!empty($p['image']) && file_exists(BASE_PATH . '/public/dist/assets/img/' . $p['image'])): ?>
                  <img src="<?= htmlspecialchars(asset('dist/assets/img/' . $p['image']), ENT_QUOTES, 'UTF-8') ?>" alt="" class="img-thumbnail" style="width:50px;height:50px;object-fit:cover;">
                <?php else: ?>
                  <span class="text-muted">No image</span>
                <?php endif; ?>
              </td>

              <td>
                <div class="fw-semibold"><?= htmlspecialchars($p['name']) ?></div>
                <small class="text-muted"><?= htmlspecialchars($p['category_name'] ?? '') ?></small>
              </td>

              <td>
                <span class="badge bg-light text-dark border"><?= htmlspecialchars($p['category_name'] ?? '') ?></span>
              </td>

              <td class="text-danger fw-bold"><?= number_format((float)$p['price']) ?> đ</td>
              <td><?= (int)$p['stock'] ?></td>

              <td>
                <?php
                  $statusMap = [
                    'active' => 'success',
                    'out_of_stock' => 'secondary',
                    'inactive' => 'danger'
                  ];
                ?>
                <span class="badge bg-<?= $statusMap[$p['status']] ?? 'secondary' ?>">
                  <?= htmlspecialchars($p['status']) ?>
                </span>
              </td>

              <td class="text-end">
                <a href="<?= BASE_URL ?>admin/products/edit&id=<?= (int)$p['product_id'] ?>" class="btn btn-sm btn-light border me-1">✏</a>
                <form action="<?= BASE_URL ?>admin/products/delete" method="post" class="d-inline" onsubmit="return confirm('Xóa sản phẩm?');">
                  <input type="hidden" name="product_id" value="<?= (int)$p['product_id'] ?>">
                  <button class="btn btn-sm btn-light border text-danger">🗑</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>

      </table>

    </div>
  </div>

</div>

<?php
$content = ob_get_clean();
view('layouts.AdminLayout', [
  'title' => 'Quản lý sản phẩm',
  'content' => $content,
]);
?>