<?php ob_start(); ?>
<div class="login-page">
  <div class="col-11 col-md-8 col-lg-4">
    <div class="card login-card shadow-lg border-0">
      <div class="login-header text-center text-white">
        <a href="<?= BASE_URL ?>" class="text-white text-decoration-none">
          <div class="brand-icon mb-2">
            <i class="bi bi-cart-fill"></i>
          </div>
          <h2><strong>Fruit Shop</strong></h2>
        </a>
        <p class="mb-0">Tạo tài khoản để mua hàng</p>
      </div>
      <div class="card-body p-4">
        <h4 class="text-center mb-4 fw-bold">Đăng ký tài khoản</h4>

        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">
            <strong>Lỗi:</strong>
            <ul class="mb-0">
              <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>register/store" method="post">
          <div class="mb-3">
            <label>Họ tên</label>
            <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($fullname ?? '') ?>" required>
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>" required>
          </div>
          <div class="mb-3">
            <label>Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Nhập lại mật khẩu</label>
            <input type="password" name="password_confirmation" class="form-control" required>
          </div>
          <div class="d-grid mb-2">
            <button class="btn btn-login btn-lg">Đăng ký</button>
          </div>
          <div class="text-center">
            Đã có tài khoản?
            <a href="<?= BASE_URL ?>login">Đăng nhập</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
view('layouts.AuthLayout', [
  'title' => 'Đăng ký tài khoản',
  'content' => $content,
]);
?>
