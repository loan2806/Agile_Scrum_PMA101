<?php
ob_start();
?>

<style>
    .login-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #ff512f, #dd2476);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-card {
        border-radius: 20px;
        overflow: hidden;
    }

    .login-header {
        background: linear-gradient(135deg, #ff7e5f, #feb47b);
        padding: 30px;
    }

    .login-header h2 {
        margin: 0;
    }

    .btn-login {
        background: #ff5722;
        color: white;
        border-radius: 10px;
    }

    .btn-login:hover {
        background: #e64a19;
        color: white;
    }

    .brand-icon {
        font-size: 40px;
    }

    .login-card:hover {
        transform: translateY(-5px);
        transition: 0.3s;
    }
</style>

<div class="login-page">

    <div class="col-11 col-md-8 col-lg-4">

        <div class="card login-card shadow-lg border-0">

            <!-- Header -->
            <div class="login-header text-center text-white">
                <a href="<?= BASE_URL ?>" class="text-white text-decoration-none">
                    <div class="brand-icon mb-2">
                        <i class="bi bi-cart-fill"></i>
                    </div>
                    <h2><strong>Fruit Shop</strong></h2>
                </a>
                <p class="mb-0">Đăng nhập để đặt mua hoa quả </p>
            </div>

            <!-- Body -->
            <div class="card-body p-4">

                <h4 class="text-center mb-4 fw-bold">
                    Đăng Nhập Hệ Thống
                </h4>

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

                <form action="<?= BASE_URL ?>check-login" method="post">
                    <input type="hidden" name="redirect" value="<?= $redirect ?? BASE_URL . 'home' ?>" />

                    <!-- Email -->
                    <div class="mb-3">
                        <label>Email</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email"
                                name="email"
                                class="form-control"
                                placeholder="Nhap email"
                                value="<?= htmlspecialchars($email ?? '') ?>"
                                required>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label>Mật khẩu</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password"
                                name="password"
                                class="form-control"
                                id="password"
                                placeholder="Nhap mat khau"
                                required>
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePass()">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="remember_me">
                        <label class="form-check-label">Ghi nhớ đăng nhập </label>
                    </div>

                    <!-- Button -->
                    <div class="d-grid">
                        <button class="btn btn-login btn-lg">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Đăng Nhập
                        </button>
                    </div>
                </form>
                <!-- Register -->
                <div class="text-center mt-3">
                    <span>Chưa có tài khoản?</span>
                    <a href="<?= BASE_URL ?>register" class="text-decoration-none fw-bold text-danger">
                        Đăng Ký Ngay
                    </a>
                </div>

                <!-- Back -->
                <div class="text-center mt-3">
                    <a href="<?= BASE_URL ?>" class="text-decoration-none">
                        ← Quay về trang chủ

                    </a>
                </div>

            </div>

        </div>

    </div>

</div>

<script>
    function togglePass() {
        const input = document.getElementById('password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>

<?php
$content = ob_get_clean();

view('layouts.AuthLayout', [
    'title' => 'Dang nhap',
    'content' => $content,
]);
?>