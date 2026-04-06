<?php ob_start(); ?>

<div class="container py-4">
  <div class="row justify-content-center">
    
    <div class="col-lg-6">

      <div class="card border-0 shadow-sm rounded-4">
        
        <!-- Header -->
        <div class="card-header bg-warning text-dark rounded-top-4">
          <h5 class="mb-0">✏️ Sửa người dùng</h5>
        </div>

        <div class="card-body p-4">

          <form method="POST" action="<?= BASE_URL . 'admin/users/update' ?>">

            <input type="hidden" name="id" value="<?= $user['user_id'] ?>">

            <!-- Avatar -->
            <div class="text-center mb-3">
              <div class="avatar-circle">
                <?= strtoupper(substr($user['username'], 0, 1)) ?>
              </div>
              <small class="text-muted d-block mt-2">Avatar người dùng</small>
            </div>

            <!-- Username -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Tên người dùng</label>
              <input name="username" value="<?= $user['username'] ?>" class="form-control">
            </div>

            <!-- Email -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Email</label>
              <input name="email" value="<?= $user['email'] ?>" class="form-control">
            </div>

            <!-- Role -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Quyền</label>
              <select name="role" class="form-select">
                <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
              </select>
            </div>

            <!-- Status -->
            <div class="mb-4">
              <label class="form-label fw-semibold">Trạng thái</label>
              <select name="status" class="form-select">
                <option value="1" <?= $user['status'] ? 'selected' : '' ?>>Hoạt động</option>
                <option value="0" <?= !$user['status'] ? 'selected' : '' ?>>Khóa</option>
              </select>
            </div>

            <!-- Action -->
            <div class="d-flex gap-2">
              <button class="btn btn-warning w-100">Cập nhật</button>
              <a href="<?= BASE_URL . 'admin/users' ?>" class="btn btn-light w-100">Hủy</a>
            </div>

          </form>

        </div>
      </div>

    </div>
  </div>
</div>

<style>
.avatar-circle {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  background: linear-gradient(135deg, #ffc107, #ff9800);
  color: #fff;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: auto;
}
</style>

<?php
$content = ob_get_clean();
view('layouts.AdminLayout', [
  'title' => 'Sửa người dùng',
  'content' => $content
]);
?>