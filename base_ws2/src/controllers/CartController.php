<?php

class CartController
{
    public function index(): void
    {
        requireLogin();
        $db = getDB();
        $cart = getCart();
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
                if ($qty <= 0) {
                    continue;
                }

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
            header('Location: ' . BASE_URL . 'home');
            exit;
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

        if ($productId <= 0) {
            setFlash('error', 'Sản phẩm không hợp lệ.');
            header('Location: ' . BASE_URL . 'home');
            exit;
        }

        $db = getDB();
        if ($db === null) {
            setFlash('error', 'Không kết nối được cơ sở dữ liệu.');
            header('Location: ' . BASE_URL . 'home');
            exit;
        }

        $stmt = $db->prepare('SELECT product_id, stock, name FROM products WHERE product_id = ? LIMIT 1');
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (!$product) {
            setFlash('error', 'Sản phẩm không tồn tại.');
            header('Location: ' . BASE_URL . 'home');
            exit;
        }

        $cart = getCart();
        $currentQty = (int) ($cart[$productId]['quantity'] ?? 0);
        $newQty = $currentQty + $quantity;

        if ($newQty > (int) $product['stock']) {
            setFlash('error', 'Số lượng vượt quá tồn kho.');
            header('Location: ' . BASE_URL . 'home');
            exit;
        }

        $cart[$productId] = [
            'quantity' => $newQty,
        ];
        saveCart($cart);

        setFlash('success', 'Đã thêm sản phẩm vào giỏ.');
        header('Location: ' . BASE_URL . 'home');
        exit;
    }

    public function update(): void
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'cart');
            exit;
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 0);
        $cart = getCart();

        if (!isset($cart[$productId])) {
            setFlash('error', 'Sản phẩm không có trong giỏ.');
            header('Location: ' . BASE_URL . 'cart');
            exit;
        }

        if ($quantity <= 0) {
            unset($cart[$productId]);
            saveCart($cart);
            setFlash('success', 'Đã xóa sản phẩm khỏi giỏ.');
            header('Location: ' . BASE_URL . 'cart');
            exit;
        }

        $db = getDB();
        $stmt = $db ? $db->prepare('SELECT stock FROM products WHERE product_id = ? LIMIT 1') : null;
        if ($stmt) {
            $stmt->execute([$productId]);
            $product = $stmt->fetch();
            if ($product && $quantity > (int) $product['stock']) {
                setFlash('error', 'Số lượng vượt quá tồn kho.');
                header('Location: ' . BASE_URL . 'cart');
                exit;
            }
        }

        $cart[$productId]['quantity'] = $quantity;
        saveCart($cart);
        setFlash('success', 'Cập nhật giỏ hàng thành công.');
        header('Location: ' . BASE_URL . 'cart');
        exit;
    }

    public function remove(): void
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'cart');
            exit;
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $cart = getCart();
        unset($cart[$productId]);
        saveCart($cart);
        setFlash('success', 'Đã xóa sản phẩm khỏi giỏ.');
        header('Location: ' . BASE_URL . 'cart');
        exit;
    }

    public function checkout(): void
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'cart');
            exit;
        }

        $cart = getCart();
        if (empty($cart)) {
            setFlash('error', 'Giỏ hàng đang rỗng.');
            header('Location: ' . BASE_URL . 'cart');
            exit;
        }

        $db = getDB();
        if ($db === null) {
            setFlash('error', 'Không kết nối được cơ sở dữ liệu.');
            header('Location: ' . BASE_URL . 'cart');
            exit;
        }

        $currentUser = getCurrentUser();
        $customerName = trim($_POST['fullname'] ?? $currentUser->name ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if ($customerName === '' || $phone === '' || $address === '') {
            setFlash('error', 'Vui lòng nhập đầy đủ thông tin giao hàng.');
            header('Location: ' . BASE_URL . 'cart');
            exit;
        }

        $ids = array_keys($cart);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $db->prepare("SELECT product_id, price, stock FROM products WHERE product_id IN ($placeholders) FOR UPDATE");

        try {
            $db->beginTransaction();
            $stmt->execute($ids);
            $products = $stmt->fetchAll();
            $productMap = [];
            foreach ($products as $product) {
                $productMap[(int) $product['product_id']] = $product;
            }

            $total = 0;
            foreach ($cart as $productId => $item) {
                $pid = (int) $productId;
                $qty = (int) $item['quantity'];
                if (!isset($productMap[$pid]) || $qty <= 0) {
                    throw new RuntimeException('Sản phẩm trong giỏ không hợp lệ.');
                }
                if ($qty > (int) $productMap[$pid]['stock']) {
                    throw new RuntimeException('Tồn kho không đủ cho đơn hàng.');
                }
                $total += $qty * (float) $productMap[$pid]['price'];
            }

            $customerStmt = $db->prepare('SELECT customer_id FROM customers WHERE user_id = ? LIMIT 1');
            $customerStmt->execute([$currentUser->id]);
            $customer = $customerStmt->fetch();

            if ($customer) {
                $customerId = (int) $customer['customer_id'];
                $updateCustomer = $db->prepare('UPDATE customers SET fullname = ?, email = ?, phone = ?, address = ? WHERE customer_id = ?');
                $updateCustomer->execute([$customerName, $currentUser->email, $phone, $address, $customerId]);
            } else {
                $insertCustomer = $db->prepare('INSERT INTO customers (fullname, email, phone, address, user_id) VALUES (?, ?, ?, ?, ?)');
                $insertCustomer->execute([$customerName, $currentUser->email, $phone, $address, $currentUser->id]);
                $customerId = (int) $db->lastInsertId();
            }

            $insertOrder = $db->prepare("INSERT INTO orders (customer_id, order_date, status, total) VALUES (?, NOW(), 'Đang xử lý', ?)");
            $insertOrder->execute([$customerId, $total]);
            $orderId = (int) $db->lastInsertId();

            $insertDetail = $db->prepare('INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
            $updateStock = $db->prepare('UPDATE products SET stock = stock - ? WHERE product_id = ?');

            foreach ($cart as $productId => $item) {
                $pid = (int) $productId;
                $qty = (int) $item['quantity'];
                $price = (float) $productMap[$pid]['price'];
                $insertDetail->execute([$orderId, $pid, $qty, $price]);
                $updateStock->execute([$qty, $pid]);
            }

            $insertPayment = $db->prepare("INSERT INTO payments (order_id, method, status, payment_date) VALUES (?, 'COD', 'Chưa thanh toán', NOW())");
            $insertPayment->execute([$orderId]);

            $db->commit();
            saveCart([]);
            setFlash('success', 'Đặt hàng thành công. Mã đơn: #' . $orderId);
            header('Location: ' . BASE_URL . 'cart');
            exit;
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            setFlash('error', $e->getMessage());
            header('Location: ' . BASE_URL . 'cart');
            exit;
        }
    }
}
