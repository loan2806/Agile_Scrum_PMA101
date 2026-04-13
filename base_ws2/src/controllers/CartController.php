<?php

class CartController
{
    public function index(): void
    {
        requireLogin();
        $db = getDB();

        // 👉 ưu tiên BUY NOW
        if (!empty($_SESSION['buy_now'])) {
            $cart = [
                $_SESSION['buy_now']['product_id'] => [
                    'quantity' => $_SESSION['buy_now']['quantity']
                ]
            ];
        } else {
            $cart = getCart();
        }

        $items = [];
        $total = 0;

        if ($db !== null && !empty($cart)) {
            $ids = array_keys($cart);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $db->prepare("SELECT product_id, name, price, stock, image FROM products WHERE product_id IN ($placeholders)");
            $stmt->execute($ids);
            $products = $stmt->fetchAll();

            foreach ($products as $product) {
                $id = (int) $product['product_id'];
                $qty = (int) ($cart[$id]['quantity'] ?? 0);
                if ($qty <= 0) continue;

                $subtotal = $qty * (float) $product['price'];
                $total += $subtotal;

                $items[] = [
                    'product_id' => $id,
                    'name' => $product['name'],
                    'price' => (float) $product['price'],
                    'stock' => (int) $product['stock'],
                    'image' => $product['image'],
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ];
            }
        }

        view('cart.index', [
            'title' => 'Giỏ hàng',
            'items' => $items,
            'total' => $total,
            'cartCount' => cartCount(),
            'flashSuccess' => getFlash('success'),
            'flashError' => getFlash('error'),
        ]);
    }

    public function add(): void
    {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=home');
            exit;
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

        $db = getDB();
        $stmt = $db->prepare('SELECT product_id, stock FROM products WHERE product_id = ?');
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (!$product) {
            setFlash('error', 'Sản phẩm không tồn tại.');
            header('Location: ' . BASE_URL . '?act=home');
            exit;
        }

        $cart = getCart();
        $currentQty = (int) ($cart[$productId]['quantity'] ?? 0);
        $newQty = $currentQty + $quantity;

        if ($newQty > (int) $product['stock']) {
            setFlash('error', 'Vượt quá tồn kho.');
            header('Location: ' . BASE_URL . '?act=home');
            exit;
        }

        $cart[$productId] = ['quantity' => $newQty];
        saveCart($cart);

        setFlash('success', 'Đã thêm vào giỏ.');
        header('Location: ' . BASE_URL . '?act=home');
        exit;
    }

    public function buyNow()
    {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL);
            exit;
        }

        $product_id = (int) ($_POST['product_id'] ?? 0);
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

        if ($product_id <= 0) {
            setFlash('error', 'Sản phẩm không hợp lệ');
            header("Location: " . BASE_URL);
            exit;
        }

        // 👉 lưu riêng sản phẩm mua ngay
        $_SESSION['buy_now'] = [
            'product_id' => $product_id,
            'quantity' => $quantity
        ];

        // 👉 quay về cart (hiển thị luôn)
        header("Location: " . BASE_URL . "?act=cart");
        exit;
    }

    public function checkout(): void
    {
        requireLogin();

        // 👉 ưu tiên BUY NOW
        if (!empty($_SESSION['buy_now'])) {
            $cart = [
                $_SESSION['buy_now']['product_id'] => [
                    'quantity' => $_SESSION['buy_now']['quantity']
                ]
            ];
        } else {
            $cart = getCart();
        }

        if (empty($cart)) {
            setFlash('error', 'Giỏ hàng rỗng.');
            header('Location: ' . BASE_URL . '?act=cart');
            exit;
        }

        // ❌ FIX LỖI: KHÔNG gọi view checkout nữa
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "?act=cart");
            exit;
        }

        $db = getDB();

        $fullname = trim($_POST['fullname'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if (!$fullname || !$phone || !$address) {
            setFlash('error', 'Thiếu thông tin');
            header('Location: ' . BASE_URL . '?act=cart');
            exit;
        }

        try {
            $db->beginTransaction();

            $total = 0;

            foreach ($cart as $pid => $item) {
                $stmt = $db->prepare("SELECT price, stock FROM products WHERE product_id = ?");
                $stmt->execute([$pid]);
                $product = $stmt->fetch();

                if (!$product || $item['quantity'] > $product['stock']) {
                    throw new Exception("Lỗi tồn kho");
                }

                $total += $item['quantity'] * $product['price'];

                $db->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?")
                   ->execute([$item['quantity'], $pid]);
            }

            $db->prepare("INSERT INTO orders (customer_id, order_date, status, total)
                          VALUES (1, NOW(), 'Đang xử lý', ?)")
               ->execute([$total]);

            $orderId = $db->lastInsertId();

            foreach ($cart as $pid => $item) {
                $db->prepare("INSERT INTO order_details (order_id, product_id, quantity, price)
                              VALUES (?, ?, ?, ?)")
                   ->execute([$orderId, $pid, $item['quantity'], $total]);
            }

            $db->commit();

            // 👉 clear dữ liệu
            saveCart([]);
            unset($_SESSION['buy_now']);

            setFlash('success', 'Đặt hàng thành công!');
            header('Location: ' . BASE_URL . '?act=cart');
            exit;

        } catch (Exception $e) {
            $db->rollBack();
            setFlash('error', $e->getMessage());
            header('Location: ' . BASE_URL . '?act=cart');
            exit;
        }
    }
    public function update(): void
{
    requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . '?act=cart');
        exit;
    }

    $productId = (int) ($_POST['product_id'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 0);

    $cart = getCart();

    if (!isset($cart[$productId])) {
        setFlash('error', 'Sản phẩm không có trong giỏ.');
        header('Location: ' . BASE_URL . '?act=cart');
        exit;
    }

    if ($quantity <= 0) {
        unset($cart[$productId]);
        saveCart($cart);
        setFlash('success', 'Đã xóa sản phẩm.');
        header('Location: ' . BASE_URL . '?act=cart');
        exit;
    }

    $db = getDB();
    $stmt = $db->prepare('SELECT stock FROM products WHERE product_id = ?');
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if ($product && $quantity > (int) $product['stock']) {
        setFlash('error', 'Vượt quá tồn kho.');
        header('Location: ' . BASE_URL . '?act=cart');
        exit;
    }

    $cart[$productId]['quantity'] = $quantity;
    saveCart($cart);

    setFlash('success', 'Cập nhật thành công.');
    header('Location: ' . BASE_URL . '?act=cart');
    exit;
}
public function remove(): void
{
    requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . '?act=cart');
        exit;
    }

    $productId = (int) ($_POST['product_id'] ?? 0);

    $cart = getCart();
    unset($cart[$productId]);
    saveCart($cart);

    setFlash('success', 'Đã xóa khỏi giỏ.');
    header('Location: ' . BASE_URL . '?act=cart');
    exit;
}
}