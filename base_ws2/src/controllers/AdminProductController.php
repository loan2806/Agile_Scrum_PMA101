<?php

class AdminProductController
{
    private function uploadImage(array $file, array &$errors): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload ảnh thất bại.';
            return '';
        }

        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts, true)) {
            $errors[] = 'Ảnh phải có định dạng: jpg, jpeg, png, webp, gif.';
            return '';
        }

        $targetDir = BASE_PATH . '/public/dist/assets/img';
        if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
            $errors[] = 'Không tạo được thư mục chứa ảnh.';
            return '';
        }

        $imageName = uniqid('prod_', true) . '.' . $ext;
        $targetPath = $targetDir . '/' . $imageName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $errors[] = 'Không thể lưu ảnh lên server.';
            return '';
        }

        return $imageName;
    }

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
            $stmt = $db->query('SELECT p.product_id, p.name, p.image, p.price, p.stock, p.unit, p.status, c.name AS category_name
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
        $image = '';

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
        if (empty($_FILES['image']['name'])) {
            $errors[] = 'Vui lòng chọn ảnh sản phẩm';
        }

        $db = getDB();
        if ($db === null) {
            $errors[] = 'Không kết nối được cơ sở dữ liệu';
        }

        if (empty($errors) && !empty($_FILES['image']['name'])) {
            $image = $this->uploadImage($_FILES['image'], $errors);
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

        $stmt = $db->prepare('INSERT INTO products (name, image, price, stock, unit, description, category_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $image, $price, $stock, $unit, $description, $categoryId, 'active']);
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
        $oldImage = trim($_POST['old_image'] ?? '');
        $image = $oldImage;

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
        if (empty($errors) && !empty($_FILES['image']['name'])) {
            $newImage = $this->uploadImage($_FILES['image'], $errors);
            if ($newImage !== '') {
                $image = $newImage;
            }
        }

        if (!empty($errors)) {
            $categories = $db ? $db->query('SELECT category_id, name FROM categories ORDER BY name')->fetchAll() : [];
            $product = $_POST;
            $product['product_id'] = $id;
            $product['image'] = $oldImage;
            view('admin.products.form', [
                'title' => 'Cập nhật sản phẩm',
                'errors' => $errors,
                'categories' => $categories,
                'product' => $product,
            ]);
            return;
        }

        $stmt = $db->prepare('UPDATE products SET name = ?, image = ?, price = ?, stock = ?, unit = ?, description = ?, category_id = ?, status = ? WHERE product_id = ?');
        $stmt->execute([$name, $image, $price, $stock, $unit, $description, $categoryId, $status, $id]);

        if (
            $oldImage !== ''
            && $image !== $oldImage
            && file_exists(BASE_PATH . '/public/dist/assets/img/' . $oldImage)
        ) {
            unlink(BASE_PATH . '/public/dist/assets/img/' . $oldImage);
        }
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

