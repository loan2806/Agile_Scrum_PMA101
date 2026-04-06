<div class="container-fluid p-0">

  <!-- Alert -->
  <?php if ($msg = getFlash('success')): ?>
    <div class="alert alert-success shadow-sm"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <?php if ($msg = getFlash('error')): ?>
    <div class="alert alert-danger shadow-sm"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">📂 Quản lý danh mục</h4>
  </div>

  <!-- Add Category -->
  <div class="card border-0 shadow mb-4 add-card">
    <div class="card-body">
      <form method="POST" action="<?= BASE_URL ?>admin/categories/store">
        <div class="row g-2 align-items-center">
          <div class="col-md-9">
            <input type="text" name="name" class="form-control"
              placeholder="Nhập tên danh mục...">
          </div>
          <div class="col-md-3">
            <button class="btn btn-primary w-100">
              ➕ Thêm danh mục
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Category List -->
  <div class="row g-3">
    <?php foreach ($categories as $c): ?>
      <div class="col-12 col-md-6 col-lg-4">

        <div class="card border-0 shadow-sm h-100 category-card">

          <div class="card-body d-flex justify-content-between align-items-center">

            <!-- Info -->
            <div>
              <h6 class="fw-bold mb-1"><?= htmlspecialchars($c['name']) ?></h6>
              <small class="text-muted">ID: #<?= (int)$c['category_id'] ?></small>
            </div>

            <div class="icon-box">
              📦
            </div>

          </div>

          <!-- Actions -->
          <div class="card-footer bg-white border-0 d-flex gap-2">

            <a href="<?= BASE_URL ?>admin/categories/edit&id=<?= (int)$c['category_id'] ?>"
              class="btn btn-outline-warning btn-sm flex-fill">
              ✏️ Sửa
            </a>

            <form method="POST" action="<?= BASE_URL ?>admin/categories/delete" class="flex-fill">
              <input type="hidden" name="id" value="<?= (int)$c['category_id'] ?>">
              <button class="btn btn-outline-danger btn-sm w-100"
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

<style>
  body {
    background: #f6f8fa;
    margin: 0;
    padding: 0;
  }

  .add-card {
    border-radius: 12px;
  }

  .category-card {
    border-radius: 14px;
    transition: all 0.25s ease;
  }

  .category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  }

  .icon-box {
    font-size: 24px;
    background: #f1f3f5;
    padding: 10px;
    border-radius: 10px;
  }

  .btn {
    border-radius: 8px;
  }

  input.form-control {
    border-radius: 8px;
  }
</style>

<?php
$content = ob_get_clean();
view('layouts.AdminLayout', [
  'title' => 'Quản lý danh mục',
  'content' => $content
]);
?>