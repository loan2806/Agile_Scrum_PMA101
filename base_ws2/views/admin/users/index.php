<?php ob_start(); ?>

<div class="container-fluid py-4 px-4">

  <!-- HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h5 class="fw-bold mb-1">Quản lý người dùng</h5>
      <small class="text-muted">Danh sách tài khoản trong hệ thống</small>
    </div>

    <a href="<?= BASE_URL . 'admin/users/create' ?>" 
       class="btn btn-primary px-3">
       + Thêm người dùng
    </a>
  </div>

  <!-- GRID -->
  <div class="row g-3">
    <?php foreach ($users as $u): ?>
      <div class="col-12 col-md-6 col-lg-4 col-xl-3">
        
        <div class="card user-card h-100 border-0 shadow-sm">

          <div class="card-body d-flex flex-column justify-content-between">

            <!-- TOP -->
            <div>

              <!-- ID + STATUS -->
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">#<?= $u['user_id'] ?></span>

                <span class="badge rounded-pill 
                  <?= $u['status'] ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?>">
                  <?= $u['status'] ? 'Hoạt động' : 'Đã khóa' ?>
                </span>
              </div>

              <!-- AVATAR + NAME -->
              <div class="d-flex align-items-center gap-2 mb-2">
                <div class="avatar">
                  <?= strtoupper(substr($u['username'], 0, 1)) ?>
                </div>

                <div>
                  <div class="fw-semibold"><?= $u['username'] ?></div>
                  <small class="text-muted"><?= $u['email'] ?></small>
                </div>
              </div>

              <!-- ROLE -->
              <div class="mb-3">
                <span class="badge role-badge">
                  <?= strtoupper($u['role']) ?>
                </span>
              </div>

            </div>

            <!-- ACTION -->
            <div class="d-flex gap-2">
              <a href="<?= BASE_URL . 'admin/users/edit&id=' . $u['user_id'] ?>" 
                 class="btn btn-outline-warning btn-sm w-100">
                 Sửa
              </a>

              <a href="<?= BASE_URL . 'admin/users/delete&id=' . $u['user_id'] ?>" 
                 onclick="return confirm('Xóa người dùng này?')" 
                 class="btn btn-outline-danger btn-sm w-100">
                 Xóa
              </a>
            </div>

          </div>

        </div>

      </div>
    <?php endforeach; ?>
  </div>

</div>

<!-- STYLE PRO -->
<style>
  body {
    background: #f8f9fb;
  }

  .user-card {
    border-radius: 14px;
    transition: all 0.2s ease;
  }

  .user-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.06);
  }

  /* Avatar */
  .avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #0d6efd;
    color: #fff;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
  }

  /* Role badge */
  .role-badge {
    background: #eef2ff;
    color: #4f46e5;
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 6px;
    font-weight: 500;
  }

  /* Buttons hover */
  .btn-outline-warning:hover {
    background: #ffc107;
    color: #000;
  }

  .btn-outline-danger:hover {
    background: #dc3545;
    color: #fff;
  }
</style>

<?php
$content = ob_get_clean();
view('layouts.AdminLayout', [
  'title' => 'Quản lý người dùng',
  'content' => $content
]);
?>