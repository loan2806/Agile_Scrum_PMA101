<?php

class ProductController
{
    // Danh sách sản phẩm
    public function index(): void
    {
        $db = getDB();
        $products = [];
        $categories = [];
        $keyword = trim($_GET['q'] ?? '');
        $categoryId = (int)($_GET['category'] ?? 0);

        if ($db) {
            $categories = $db->query('SELECT category_id, name FROM categories ORDER BY name')->fetchAll();

            $sql = 'SELECT p.product_id, p.sku, p.name, p.slug, p.price, p.sale_price, p.stock, p.unit, p.origin, p.weight_gram, p.is_featured, p.is_new, p.status, p.description, p.image, c.name AS category_name
                    FROM products p
                    LEFT JOIN categories c ON c.category_id = p.category_id
                    WHERE 1=1';
            $params = [];

            if ($keyword !== '') {
                $sql .= ' AND p.name LIKE ?';
                $params[] = '%' . $keyword . '%';
            }
            if ($categoryId > 0) {
                $sql .= ' AND p.category_id = ?';
                $params[] = $categoryId;
            }

            $sql .= ' ORDER BY p.product_id DESC';
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $products = $stmt->fetchAll();
        }

        view('products.index', [
            'title' => 'Tất cả sản phẩm',
            'products' => $products,
            'categories' => $categories,
            'keyword' => $keyword,
            'categoryId' => $categoryId,
            'flashSuccess' => getFlash('success'),
            'flashError' => getFlash('error'),
        ]);
    }

    // Thêm sản phẩm
    public function store(): void
    {
        $db = getDB();
        $errors = [];

        $name = trim($_POST['name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $sale_price = (float)($_POST['sale_price'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);
        $unit = trim($_POST['unit'] ?? 'kg');
        $origin = trim($_POST['origin'] ?? '');
        $weight_gram = (int)($_POST['weight_gram'] ?? 0);
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $is_new = isset($_POST['is_new']) ? 1 : 0;
        $status = $_POST['status'] ?? 'active';
        $description = trim($_POST['description'] ?? '');

        if ($name === '') $errors[] = 'Tên sản phẩm bắt buộc';
        if ($category_id <= 0) $errors[] = 'Danh mục bắt buộc';

        // Upload ảnh
        $image = '';
        if (!empty($_FILES['image']['name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($ext, $allowed)) {
                $errors[] = 'Chỉ chấp nhận file ảnh: jpg, jpeg, png, gif';
            } else {
                $imageName = uniqid() . '.' . $ext;
                $uploadPath = __DIR__ . '/../../public/dist/assets/img/' . $imageName;
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $errors[] = 'Không thể upload ảnh';
                } else {
                    $image = $imageName;
                }
            }
        } else {
            $errors[] = 'Ảnh sản phẩm bắt buộc';
        }

        if ($errors) {
            setFlash('error', implode('<br>', $errors));
            header('Location: ' . BASE_URL . 'admin/products/create');
            exit;
        }

        // Tạo slug và SKU
        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $name));
        $sku = 'SKU-' . strtoupper(uniqid());

        $stmt = $db->prepare('INSERT INTO products 
            (sku, name, slug, price, sale_price, stock, unit, origin, weight_gram, is_featured, is_new, status, description, image, category_id, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
        $stmt->execute([
            $sku,
            $name,
            $slug,
            $price,
            $sale_price,
            $stock,
            $unit,
            $origin,
            $weight_gram,
            $is_featured,
            $is_new,
            $status,
            $description,
            $image,
            $category_id
        ]);

        setFlash('success', 'Thêm sản phẩm thành công');
        header('Location: ' . BASE_URL . 'admin/products');
    }

    // Cập nhật sản phẩm
    public function update(): void
    {
        $db = getDB();
        $errors = [];

        $product_id = (int)($_POST['product_id'] ?? 0);
        if ($product_id <= 0) {
            setFlash('error', 'ID sản phẩm không hợp lệ');
            header('Location: ' . BASE_URL . 'admin/products');
            exit;
        }

        // Lấy sản phẩm cũ
        $stmt = $db->prepare('SELECT image FROM products WHERE product_id=? LIMIT 1');
        $stmt->execute([$product_id]);
        $oldProduct = $stmt->fetch();
        $oldImage = $oldProduct['image'] ?? '';

        $name = trim($_POST['name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $sale_price = (float)($_POST['sale_price'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);
        $unit = trim($_POST['unit'] ?? 'kg');
        $origin = trim($_POST['origin'] ?? '');
        $weight_gram = (int)($_POST['weight_gram'] ?? 0);
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $is_new = isset($_POST['is_new']) ? 1 : 0;
        $status = $_POST['status'] ?? 'active';
        $description = trim($_POST['description'] ?? '');
        $image = $oldImage;

        if ($name === '') $errors[] = 'Tên sản phẩm bắt buộc';
        if ($category_id <= 0) $errors[] = 'Danh mục bắt buộc';

        // Upload ảnh mới nếu có
        if (!empty($_FILES['image']['name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $imageName = uniqid() . '.' . $ext;
            $uploadPath = __DIR__ . '/../../public/dist/assets/img/' . $imageName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $image = $imageName;
                // Xóa ảnh cũ
                if ($oldImage && file_exists(__DIR__ . '/../../public/dist/assets/img/' . $oldImage)) {
                    unlink(__DIR__ . '/../../public/dist/assets/img/' . $oldImage);
                }
            } else {
                $errors[] = 'Không thể upload ảnh mới';
            }
        }

        if ($errors) {
            setFlash('error', implode('<br>', $errors));
            header('Location: ' . BASE_URL . 'admin/products/edit&id=' . $product_id);
            exit;
        }

        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $name));

        $stmt = $db->prepare('UPDATE products SET
            name=?, slug=?, price=?, sale_price=?, stock=?, unit=?, origin=?, weight_gram=?, is_featured=?, is_new=?, status=?, description=?, image=?, category_id=?, updated_at=NOW()
            WHERE product_id=?');
        $stmt->execute([
            $name,
            $slug,
            $price,
            $sale_price,
            $stock,
            $unit,
            $origin,
            $weight_gram,
            $is_featured,
            $is_new,
            $status,
            $description,
            $image,
            $category_id,
            $product_id
        ]);

        setFlash('success', 'Cập nhật sản phẩm thành công');
        header('Location: ' . BASE_URL . 'admin/products');
    }

    // Xóa sản phẩm
    public function delete(): void
    {
        $db = getDB();
        $product_id = (int)($_POST['product_id'] ?? 0);
        if ($product_id <= 0) {
            setFlash('error', 'ID sản phẩm không hợp lệ');
            header('Location: ' . BASE_URL . 'admin/products');
            exit;
        }

        // Lấy ảnh để xóa
        $stmt = $db->prepare('SELECT image FROM products WHERE product_id=? LIMIT 1');
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        $image = $product['image'] ?? '';

        // Xóa DB
        $stmt = $db->prepare('DELETE FROM products WHERE product_id=?');
        $stmt->execute([$product_id]);

        // Xóa ảnh
        if ($image && file_exists(__DIR__ . '/../../public/dist/assets/img/' . $image)) {
            unlink(__DIR__ . '/../../public/dist/assets/img/' . $image);
        }

        setFlash('success', 'Xóa sản phẩm thành công');
        header('Location: ' . BASE_URL . 'admin/products');
    }
    public function show($id)
    {
        $db = getDB();

        $stmt = $db->prepare('SELECT p.*, c.name AS category_name
                          FROM products p
                          LEFT JOIN categories c ON c.category_id = p.category_id
                          WHERE p.product_id = ? LIMIT 1');
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if (!$product) {
            die("Sản phẩm không tồn tại");
        }

        view('products/show', [
            'product' => $product
        ]);
    }
}
