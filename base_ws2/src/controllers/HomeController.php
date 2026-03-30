<?php
// Controller chịu trách nhiệm xử lý logic cho các trang cơ bản
class HomeController
{
    // Trang welcome - hiển thị cho người chưa đăng nhập
    // Nếu đã đăng nhập thì redirect về trang home
    public function welcome(): void
    {
        header('Location: ' . BASE_URL . 'home');
        exit;
    }

    // Trang home - chỉ dành cho người đã đăng nhập
    // Nếu chưa đăng nhập thì redirect về trang welcome
    public function home(): void
    {
        $currentUser = getCurrentUser();
        $db = getDB();
        $categories = [];
        $featuredProducts = [];
        $vietnamProducts = [];
        $importedProducts = [];

        if ($db !== null) {
            $categories = $db->query('SELECT category_id, name FROM categories ORDER BY name')->fetchAll();
            $featuredProducts = $db->query('SELECT product_id, name, price, stock, description, image FROM products ORDER BY product_id DESC LIMIT 8')->fetchAll();
            $vnStmt = $db->prepare('SELECT p.product_id, p.name, p.price, p.stock, p.description, p.image
                                    FROM products p
                                    INNER JOIN categories c ON c.category_id = p.category_id
                                    WHERE c.name LIKE ?
                                    ORDER BY p.product_id DESC LIMIT 8');
            $vnStmt->execute(['%Viet%']);
            $vietnamProducts = $vnStmt->fetchAll();

            $importStmt = $db->prepare('SELECT p.product_id, p.name, p.price, p.stock, p.description, p.image
                                        FROM products p
                                        INNER JOIN categories c ON c.category_id = p.category_id
                                        WHERE c.name LIKE ?
                                        ORDER BY p.product_id DESC LIMIT 8');
            $importStmt->execute(['%nhap%']);
            $importedProducts = $importStmt->fetchAll();
        }

        view('home', [
            'title' => 'Morning Fruit Clone',
            'user' => $currentUser,
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'vietnamProducts' => $vietnamProducts,
            'importedProducts' => $importedProducts,
            'cartCount' => cartCount(),
            'flashSuccess' => getFlash('success'),
            'flashError' => getFlash('error'),
        ]);
    }

    // Trang hiển thị khi route không tồn tại
    public function notFound(): void
    {
        http_response_code(404);
        // Hiển thị view not_found với dữ liệu title
        view('not_found', [
            'title' => 'Không tìm thấy trang',
        ]);
    }
}
