<?php

// CONFIG
$config = require __DIR__ . '/config/config.php';

// HELPERS
require_once __DIR__ . '/src/helpers/helpers.php';
require_once __DIR__ . '/src/helpers/database.php';

// MODELS
require_once __DIR__ . '/src/models/User.php';

// CONTROLLERS
require_once __DIR__ . '/src/controllers/HomeController.php';
require_once __DIR__ . '/src/controllers/AuthController.php';
require_once __DIR__ . '/src/controllers/CartController.php';
require_once __DIR__ . '/src/controllers/ProductController.php';
require_once __DIR__ . '/src/controllers/OrderController.php';
require_once __DIR__ . '/src/controllers/AdminProductController.php';
require_once __DIR__ . '/src/controllers/CategoryController.php'; // 🔥 THÊM
require_once __DIR__ . '/src/controllers/UserController.php'; // 🔥 THÊM

// INIT CONTROLLER
$homeController = new HomeController();
$authController = new AuthController();
$cartController = new CartController();
$productController = new ProductController();
$orderController = new OrderController();
$adminProductController = new AdminProductController();
$categoryController = new CategoryController(); // 🔥 THÊM
$userController = new UserController(); // 🔥 THÊM

// ROUTE
$act = $_GET['act'] ?? '/';

// PRODUCT DETAIL
if (preg_match('#^products/(\d+)$#', $act, $matches)) {
    $productController->show((int) $matches[1]);
    exit;
}

match ($act) {

    // ================= PUBLIC =================
    '/', 'welcome' => $homeController->welcome(),
    'home' => $homeController->home(),

    // ================= AUTH =================
    'login' => $authController->login(),
    'check-login' => $authController->checkLogin(),
    'register' => $authController->register(),
    'register/store' => $authController->storeRegister(),
    'logout' => $authController->logout(),

    // ================= PRODUCT =================
    'products' => $productController->index(),

    // ================= CART =================
    'cart' => $cartController->index(),
    'cart/add' => $cartController->add(),
    'cart/update' => $cartController->update(),
    'cart/remove' => $cartController->remove(),
    'checkout' => $cartController->checkout(),
    'checkout/buy-now' => $cartController->buyNow(),

    // ================= USER =================
    'my-orders' => $orderController->myOrders(),
    'order-detail' => $orderController->detail(),
    'profile' => $orderController->profile(),
    'profile/update' => $orderController->updateProfile(),

    // ================= ADMIN PRODUCT =================
    'admin/products' => $adminProductController->index(),
    'admin/products/create' => $adminProductController->create(),
    'admin/products/store' => $adminProductController->store(),
    'admin/products/edit' => $adminProductController->edit(),
    'admin/products/update' => $adminProductController->update(),
    'admin/products/delete' => $adminProductController->delete(),

    // ================= ADMIN CATEGORY =================
    'admin/categories' => $categoryController->index(),
    'admin/categories/store' => $categoryController->store(),
    'admin/categories/delete' => $categoryController->delete(),
    'admin/categories/edit' => $categoryController->edit(),

    // ================= ADMIN ORDER =================
    'admin/orders' => $orderController->adminOrders(),
    'admin/orders/update' => $orderController->updateStatus(),
    'admin/dashboard' => $orderController->dashboard(),

    //==================ADMIN USERS===================
    'admin/users' => $userController->index(),
    'admin/users/create' => $userController->create(),
    'admin/users/store' => $userController->store(),
    'admin/users/edit' => $userController->edit(),
    'admin/users/update' => $userController->update(),
    'admin/users/delete' => $userController->delete(),
    // ================= 404 =================
    default => $homeController->notFound(),
};
