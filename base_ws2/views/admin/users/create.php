<?php ob_start(); ?>

<div class="container py-4">
  <div class="row justify-content-center">
    
    <div class="col-lg-6">

      <div class="card border-0 shadow-sm rounded-4">
        
        <!-- Header -->
        <div class="card-header bg-primary text-white rounded-top-4">
          <h5 class="mb-0">➕ Thêm người dùng</h5>
        </div>

        <div class="card-body p-4">

          <form method="POST" action="<?= BASE_URL . 'admin/users/store' ?>">

            <!-- Avatar preview -->
            <div class="text-center mb-3">
              <div class="avatar-circle">U</div>
              <small class="text-muted d-block mt-2">Avatar mặc định</small>
            </div>

            <!-- Username -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Tên người dùng</label>
              <input name="username" class="form-control" placeholder="Nhập username" required>
            </div>

            <!-- Email -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Email</label>
              <input name="email" type="email" class="form-control" placeholder="Nhập email" required>
            </div>

            <!-- Password -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Mật khẩu</label>
              <input name="password" type="password" class="form-control" placeholder="Nhập mật khẩu" required>
            </div>

            <!-- Role -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Quyền</label>
              <select name="role" class="form-select">
                <option value="user">User</option>
                <option value="admin">Admin</option>
              </select>
            </div>

            <!-- Status -->
            <div class="mb-4">
              <label class="form-label fw-semibold">Trạng thái</label>
              <select name="status" class="form-select">
                <option value="1">Hoạt động</option>
                <option value="0">Khóa</option>
              </select>
            </div>

            <!-- Action -->
            <div class="d-flex gap-2">
              <button class="btn btn-primary w-100">💾 Lưu</button>
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
  background: linear-gradient(135deg, #0d6efd, #4f46e5);
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
  'title' => 'Thêm người dùng',
  'content' => $content
]);
?>