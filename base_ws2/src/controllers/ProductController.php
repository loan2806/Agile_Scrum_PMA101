<?php

class ProductController
{
    public function index(): void
    {
        $db = getDB();
        $products = [];
        $categories = [];
        $keyword = trim($_GET['q'] ?? '');
        $categoryId = (int) ($_GET['category'] ?? 0);

        if ($db !== null) {
            $categories = $db->query('SELECT category_id, name FROM categories ORDER BY name')->fetchAll();
            $sql = 'SELECT p.product_id, p.name, p.price, p.stock, p.description, p.image, c.name AS category_name
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

    public function show(int $id): void
    {
        $db = getDB();
        if ($db === null) {
            http_response_code(500);
            echo 'Không kết nối được cơ sở dữ liệu';
            return;
        }

        $stmt = $db->prepare('SELECT p.product_id, p.name, p.price, p.stock, p.description, p.image, c.name AS category_name
                              FROM products p
                              LEFT JOIN categories c ON c.category_id = p.category_id
                              WHERE p.product_id = ? LIMIT 1');
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if (!$product) {
            http_response_code(404);
            view('not_found', ['title' => 'Không tìm thấy sản phẩm']);
            return;
        }

        $relatedStmt = $db->prepare('SELECT product_id, name, price FROM products WHERE category_id = (SELECT category_id FROM products WHERE product_id = ?) AND product_id <> ? ORDER BY product_id DESC LIMIT 4');
        $relatedStmt->execute([$id, $id]);
        $relatedProducts = $relatedStmt->fetchAll();

        view('products.show', [
            'title' => $product['name'],
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'flashSuccess' => getFlash('success'),
            'flashError' => getFlash('error'),
        ]);
    }
}
