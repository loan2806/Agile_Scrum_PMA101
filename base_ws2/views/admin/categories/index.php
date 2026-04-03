
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

  <!-- Top bar: Thêm danh mục -->
  <div class="card border-0 shadow-sm mb-3 p-3">
    <h6>📂 Thêm danh mục mới</h6>
    <form method="POST" action="<?= BASE_URL ?>admin/categories/store">
      <div class="input-group input-group-sm">
        <span class="input-group-text">📁</span>
        <input type="text" name="name" class="form-control" placeholder="Nhập tên danh mục...">
        <button class="btn btn-primary px-3">Thêm</button>
      </div>
    </form>
  </div>

  <!-- Grid danh mục -->
  <div class="card border-0 shadow-sm">
    <div class="card-body py-2 px-3">
      <div class="row g-3">
        <?php foreach ($categories as $c): ?>
          <div class="col-12 col-md-6">
            <div class="card h-100 border-0 shadow-sm category-card">
              <div class="card-body py-3 px-3 d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-semibold"><?= htmlspecialchars($c['name']) ?></div>
                  <small class="text-muted">ID: #<?= (int)$c['category_id'] ?></small>
                </div>
                <div class="fs-5">📦</div>
              </div>
              <div class="card-footer bg-white border-0 p-2 d-flex gap-2">
                <!-- Edit -->
                <a href="<?= BASE_URL ?>admin/categories/edit&id=<?= (int)$c['category_id'] ?>"
                   class="btn btn-warning btn-sm flex-fill">✏️ Chỉnh sửa</a>

                <!-- Delete -->
                <form method="POST" action="<?= BASE_URL ?>admin/categories/delete" class="flex-fill">
                  <input type="hidden" name="id" value="<?= (int)$c['category_id'] ?>">
                  <button class="btn btn-danger btn-sm w-100"
                          onclick="return confirm('Xóa danh mục này?')">
                    🗑 Xóa
                  </button>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

</div>

<style>
  .category-card { border-radius:10px; transition:0.2s; }
  .category-card:hover { transform:translateY(-3px); box-shadow:0 6px 15px rgba(0,0,0,0.08);}
</style>

<?php
$content = ob_get_clean();
view('layouts.AdminLayout', [
  'title' => 'Quản lý danh mục',
  'content' => $content
]);
?>