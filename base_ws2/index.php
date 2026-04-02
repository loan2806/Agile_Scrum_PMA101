<?php

// Nạp cấu hình chung của ứng dụng
$config = require __DIR__ . '/config/config.php';

// Nạp các file chứa hàm trợ giúp
require_once __DIR__ . '/src/helpers/helpers.php'; // Helper chứa các hàm trợ giúp (hàm xử lý view, block, asset, session, ...)
require_once __DIR__ . '/src/helpers/database.php'; // Helper kết nối database(kết nối với cơ sở dữ liệu)

// Nạp các file chứa model
require_once __DIR__ . '/src/models/User.php';

// Nạp các file chứa controller
require_once __DIR__ . '/src/controllers/HomeController.php';
require_once __DIR__ . '/src/controllers/AuthController.php';
require_once __DIR__ . '/src/controllers/CartController.php';
require_once __DIR__ . '/src/controllers/ProductController.php';
require_once __DIR__ . '/src/controllers/OrderController.php';
require_once __DIR__ . '/src/controllers/AdminProductController.php';

// Khởi tạo các controller
$homeController = new HomeController();
$authController = new AuthController();
$cartController = new CartController();
$productController = new ProductController();
$orderController = new OrderController();
$adminProductController = new AdminProductController();

// Xác định route dựa trên tham số act (mặc định là trang chủ '/')
$act = $_GET['act'] ?? '/';

if (preg_match('#^products/(\d+)$#', $act, $matches)) {
    $productController->show((int) $matches[1]);
    exit;
}

// Match đảm bảo chỉ một action tương ứng được gọi
match ($act) {
    // Trang welcome (cho người chưa đăng nhập) - mặc định khi truy cập '/'
    '/', 'welcome' => $homeController->welcome(),

    // Trang home (cho người đã đăng nhập)
    'home' => $homeController->home(),

    // Đường dẫn đăng nhập, đăng xuất
    'login' => $authController->login(),
    'check-login' => $authController->checkLogin(),
    'register' => $authController->register(),
    'register/store' => $authController->storeRegister(),
    'logout' => $authController->logout(),
    'cart' => $cartController->index(),
    'cart/add' => $cartController->add(),
    'cart/update' => $cartController->update(),
    'cart/remove' => $cartController->remove(),
    'checkout' => $cartController->checkout(),
    'products' => $productController->index(),
    'my-orders' => $orderController->myOrders(),
    'profile' => $orderController->profile(),
    'profile/update' => $orderController->updateProfile(),

    // Admin - sản phẩm
    'admin/products' => $adminProductController->index(),
    'admin/products/create' => $adminProductController->create(),
    'admin/products/store' => $adminProductController->store(),
    'admin/products/edit' => $adminProductController->edit(),
    'admin/products/update' => $adminProductController->update(),
    'admin/products/delete' => $adminProductController->delete(),

    // Đường dẫn không tồn tại
    default => $homeController->notFound(),
};
