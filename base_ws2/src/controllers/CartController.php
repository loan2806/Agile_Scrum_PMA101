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

        $checkoutOld = $_SESSION['checkout_old'] ?? null;
        $checkoutErrors = $_SESSION['checkout_errors'] ?? [];
        unset($_SESSION['checkout_old'], $_SESSION['checkout_errors']);

        view('cart.index', [
            'title' => 'Giỏ hàng',
            'items' => $items,
            'total' => $total,
            'cartCount' => cartCount(),
            'flashSuccess' => getFlash('success'),
            'flashError' => getFlash('error'),
            'checkoutOld' => $checkoutOld,
            'checkoutErrors' => $checkoutErrors,
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

        $fullname = trim((string) ($_POST['fullname'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $address = trim((string) ($_POST['address'] ?? ''));
        $paymentMethod = $_POST['payment_method'] ?? 'cash';
        if (!in_array($paymentMethod, ['cash', 'transfer'], true)) {
            $paymentMethod = 'cash';
        }
        $paymentDb = $paymentMethod === 'transfer' ? 'bank_transfer' : 'cash';

        [$checkoutErrors, $phoneNormalized] = $this->validateCheckoutForm($fullname, $phone, $address, $paymentMethod);
        if (!empty($checkoutErrors)) {
            $_SESSION['checkout_old'] = [
                'fullname' => $fullname,
                'phone' => $phone,
                'address' => $address,
                'payment_method' => $paymentMethod,
            ];
            $_SESSION['checkout_errors'] = $checkoutErrors;
            setFlash('error', 'Vui lòng kiểm tra lại thông tin đặt hàng.');
            header('Location: ' . BASE_URL . '?act=cart');
            exit;
        }

        $phone = $phoneNormalized;

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

            $db->prepare("INSERT INTO orders (customer_id, order_date, status, total, payment_method)
                          VALUES (1, NOW(), 'Đang xử lý', ?, ?)")
               ->execute([$total, $paymentDb]);

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

    private static function normalizeVnPhone(string $raw): string
    {
        $d = preg_replace('/\D+/', '', $raw);
        if (str_starts_with($d, '84') && strlen($d) >= 10) {
            $d = '0' . substr($d, 2);
        }
        return $d;
    }

    /**
     * @return array{0: array<int, string>, 1: string}
     */
    private function validateCheckoutForm(string $fullname, string $phone, string $address, string $paymentMethod): array
    {
        $errors = [];

        $nameLen = mb_strlen($fullname);
        if ($nameLen < 2) {
            $errors[] = 'Họ tên phải có ít nhất 2 ký tự.';
        } elseif ($nameLen > 120) {
            $errors[] = 'Họ tên không được vượt quá 120 ký tự.';
        } elseif (!preg_match('/\p{L}/u', $fullname)) {
            $errors[] = 'Họ tên phải có ít nhất một chữ cái.';
        }

        $phoneNorm = self::normalizeVnPhone($phone);
        if ($phoneNorm === '') {
            $errors[] = 'Vui lòng nhập số điện thoại.';
        } elseif (!preg_match('/^0[0-9]{9,10}$/', $phoneNorm)) {
            $errors[] = 'Số điện thoại không hợp lệ (10–11 chữ số, bắt đầu bằng 0, ví dụ 09xxxxxxxx).';
        }

        $addrLen = mb_strlen($address);
        if ($addrLen < 10) {
            $errors[] = 'Địa chỉ giao hàng phải có ít nhất 10 ký tự.';
        } elseif ($addrLen > 500) {
            $errors[] = 'Địa chỉ không được vượt quá 500 ký tự.';
        }

        if (!in_array($paymentMethod, ['cash', 'transfer'], true)) {
            $errors[] = 'Phương thức thanh toán không hợp lệ.';
        }

        return [$errors, $phoneNorm];
    }
}