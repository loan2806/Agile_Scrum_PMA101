<?php

class AdminProductController
{
    private function requireAdminAccess(): void
    {
        requireLogin();
        if (!isAdmin()) {
            header('Location: ' . BASE_URL . 'home');
            exit;
        }
    }

    public function index(): void
    {
        $this->requireAdminAccess();
        $db = getDB();
        $products = [];
        if ($db !== null) {
            $stmt = $db->query('SELECT p.product_id, p.name, p.price, p.stock, p.unit, p.status, c.name AS category_name
                                FROM products p
                                LEFT JOIN categories c ON c.category_id = p.category_id
                                ORDER BY p.product_id DESC');
            $products = $stmt->fetchAll();
        }

        $content = view('admin.products.index', [
            'products' => $products,
        ]);
    }

    public function create(): void
    {
        $this->requireAdminAccess();
        $db = getDB();
        $categories = $db ? $db->query('SELECT category_id, name FROM categories ORDER BY name')->fetchAll() : [];
        view('admin.products.form', [
            'title' => 'Thêm sản phẩm',
            'categories' => $categories,
        ]);
    }

    public function store(): void
    {
        $this->requireAdminAccess();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'admin/products');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $price = (float) ($_POST['price'] ?? 0);
        $stock = (int) ($_POST['stock'] ?? 0);
        $unit = trim($_POST['unit'] ?? 'kg');
        $description = trim($_POST['description'] ?? '');
        $categoryId = (int) ($_POST['category_id'] ?? 0);

        $errors = [];
        if ($name === '') {
            $errors[] = 'Vui lòng nhập tên sản phẩm';
        }
        if ($price <= 0) {
            $errors[] = 'Giá phải lớn hơn 0';
        }
        if ($categoryId <= 0) {
            $errors[] = 'Vui lòng chọn danh mục';
        }

        $db = getDB();
        if ($db === null) {
            $errors[] = 'Không kết nối được cơ sở dữ liệu';
        }

        if (!empty($errors)) {
            $categories = $db ? $db->query('SELECT category_id, name FROM categories ORDER BY name')->fetchAll() : [];
            view('admin.products.form', [
                'title' => 'Thêm sản phẩm',
                'errors' => $errors,
                'categories' => $categories,
                'old' => $_POST,
            ]);
            return;
        }

        $stmt = $db->prepare('INSERT INTO products (name, price, stock, unit, description, category_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $price, $stock, $unit, $description, $categoryId, 'active']);
        setFlash('success', 'Thêm sản phẩm thành công.');
        header('Location: ' . BASE_URL . 'admin/products');
        exit;
    }

    public function edit(): void
    {
        $this->requireAdminAccess();
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . BASE_URL . 'admin/products');
            exit;
        }

        $db = getDB();
        if ($db === null) {
            setFlash('error', 'Không kết nối được cơ sở dữ liệu.');
            header('Location: ' . BASE_URL . 'admin/products');
            exit;
        }

        $stmt = $db->prepare('SELECT * FROM products WHERE product_id = ? LIMIT 1');
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if (!$product) {
            setFlash('error', 'Sản phẩm không tồn tại.');
            header('Location: ' . BASE_URL . 'admin/products');
            exit;
        }

        $categories = $db->query('SELECT category_id, name FROM categories ORDER BY name')->fetchAll();
        view('admin.products.form', [
            'title' => 'Cập nhật sản phẩm',
            'categories' => $categories,
            'product' => $product,
        ]);
    }

    public function update(): void
    {
        $this->requireAdminAccess();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'admin/products');
            exit;
        }

        $id = (int) ($_POST['product_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $price = (float) ($_POST['price'] ?? 0);
        $stock = (int) ($_POST['stock'] ?? 0);
        $unit = trim($_POST['unit'] ?? 'kg');
        $description = trim($_POST['description'] ?? '');
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $status = $_POST['status'] ?? 'active';

        if ($id <= 0) {
            header('Location: ' . BASE_URL . 'admin/products');
            exit;
        }

        $errors = [];
        if ($name === '') {
            $errors[] = 'Vui lòng nhập tên sản phẩm';
        }
        if ($price <= 0) {
            $errors[] = 'Giá phải lớn hơn 0';
        }
        if ($categoryId <= 0) {
            $errors[] = 'Vui lòng chọn danh mục';
        }

        $db = getDB();
        if ($db === null) {
            $errors[] = 'Không kết nối được cơ sở dữ liệu';
        }

        if (!empty($errors)) {
            $categories = $db ? $db->query('SELECT category_id, name FROM categories ORDER BY name')->fetchAll() : [];
            $product = $_POST;
            $product['product_id'] = $id;
            view('admin.products.form', [
                'title' => 'Cập nhật sản phẩm',
                'errors' => $errors,
                'categories' => $categories,
                'product' => $product,
            ]);
            return;
        }

        $stmt = $db->prepare('UPDATE products SET name = ?, price = ?, stock = ?, unit = ?, description = ?, category_id = ?, status = ? WHERE product_id = ?');
        $stmt->execute([$name, $price, $stock, $unit, $description, $categoryId, $status, $id]);
        setFlash('success', 'Cập nhật sản phẩm thành công.');
        header('Location: ' . BASE_URL . 'admin/products');
        exit;
    }

    public function delete(): void
    {
        $this->requireAdminAccess();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'admin/products');
            exit;
        }

        $id = (int) ($_POST['product_id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . BASE_URL . 'admin/products');
            exit;
        }

        $db = getDB();
        if ($db !== null) {
            $stmt = $db->prepare('DELETE FROM products WHERE product_id = ?');
            $stmt->execute([$id]);
        }
        setFlash('success', 'Đã xóa sản phẩm.');
        header('Location: ' . BASE_URL . 'admin/products');
        exit;
    }
}

