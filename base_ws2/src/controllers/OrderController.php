<?php

class OrderController
{
    // ================= USER =================

    public function myOrders(): void
    {
        requireLogin();
        $db = getDB();
        $orders = [];
        $currentUser = getCurrentUser();

        if ($db !== null) {
            $stmt = $db->prepare('SELECT o.order_id, o.order_date, o.status, o.total
                                  FROM orders o
                                  INNER JOIN customers c ON c.customer_id = o.customer_id
                                  WHERE c.user_id = ?
                                  ORDER BY o.order_id DESC');
            $stmt->execute([$currentUser->id]);
            $orders = $stmt->fetchAll();
        }

        view('orders.my_orders', [
            'title' => 'Đơn hàng của tôi',
            'orders' => $orders,
            'flashSuccess' => getFlash('success'),
            'flashError' => getFlash('error'),
        ]);
    }

    public function detail(): void
    {
        requireLogin();
        $db = getDB();
        $orderId = (int) ($_GET['id'] ?? 0);

        $order = null;
        $items = [];

        if ($db !== null && $orderId > 0) {
            $stmt = $db->prepare("SELECT * FROM orders WHERE order_id = ?");
            $stmt->execute([$orderId]);
            $order = $stmt->fetch();

            $stmt = $db->prepare("
                SELECT od.*, p.name
                FROM order_details od
                JOIN products p ON p.product_id = od.product_id
                WHERE od.order_id = ?
            ");
            $stmt->execute([$orderId]);
            $items = $stmt->fetchAll();
        }

        view('orders.detail', [
            'order' => $order,
            'items' => $items
        ]);
    }

    // ================= PROFILE =================

    public function profile(): void
    {
        requireLogin();
        $db = getDB();
        $profile = null;
        $currentUser = getCurrentUser();

        if ($db !== null) {
            $stmt = $db->prepare('SELECT fullname, phone, address FROM customers WHERE user_id = ? LIMIT 1');
            $stmt->execute([$currentUser->id]);
            $profile = $stmt->fetch();
        }

        view('account.profile', [
            'title' => 'Tài khoản',
            'profile' => $profile,
            'flashSuccess' => getFlash('success'),
            'flashError' => getFlash('error'),
        ]);
    }

    public function updateProfile(): void
    {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=profile');
            exit;
        }

        $fullname = trim($_POST['fullname'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if ($fullname === '' || $phone === '' || $address === '') {
            setFlash('error', 'Vui lòng nhập đầy đủ thông tin.');
            header('Location: ' . BASE_URL . '?act=profile');
            exit;
        }

        $db = getDB();
        $currentUser = getCurrentUser();

        if ($db !== null) {
            $stmt = $db->prepare('SELECT customer_id FROM customers WHERE user_id = ? LIMIT 1');
            $stmt->execute([$currentUser->id]);
            $row = $stmt->fetch();

            if ($row) {
                $update = $db->prepare('UPDATE customers SET fullname = ?, phone = ?, address = ? WHERE customer_id = ?');
                $update->execute([$fullname, $phone, $address, $row['customer_id']]);
            } else {
                $insert = $db->prepare('INSERT INTO customers (fullname, email, phone, address, user_id) VALUES (?, ?, ?, ?, ?)');
                $insert->execute([$fullname, $currentUser->email, $phone, $address, $currentUser->id]);
            }
        }

        setFlash('success', 'Cập nhật thông tin thành công.');
        header('Location: ' . BASE_URL . '?act=profile');
        exit;
    }

    // ================= ADMIN =================

    // ✅ Danh sách đơn hàng
    public function adminOrders(): void
    {
        requireAdmin();

        $db = getDB();
        $orders = [];

        if ($db !== null) {
            $orders = $db->query("
                SELECT o.*, c.fullname
                FROM orders o
                JOIN customers c ON o.customer_id = c.customer_id
                ORDER BY o.order_id DESC
            ")->fetchAll();
        }

        view('admin.orders.index', [
            'orders' => $orders
        ]);
    }

    // ❗ THÊM MỚI: Xem chi tiết đơn hàng (ADMIN)
    public function adminOrderDetail(): void
    {
        requireAdmin();

        $db = getDB();
        $orderId = (int) ($_GET['id'] ?? 0);

        $order = null;
        $items = [];

        if ($db !== null && $orderId > 0) {
            $stmt = $db->prepare("
                SELECT o.*, c.fullname, c.phone, c.address
                FROM orders o
                LEFT JOIN customers c ON c.customer_id = o.customer_id
                WHERE o.order_id = ?
            ");
            $stmt->execute([$orderId]);
            $order = $stmt->fetch();

            $stmt = $db->prepare("
                SELECT od.*, p.name
                FROM order_details od
                JOIN products p ON p.product_id = od.product_id
                WHERE od.order_id = ?
            ");
            $stmt->execute([$orderId]);
            $items = $stmt->fetchAll();
        }

        if ($orderId <= 0 || empty($order)) {
            setFlash('error', 'Không tìm thấy đơn hàng.');
            header('Location: ' . BASE_URL . '?act=admin/orders');
            exit;
        }

        view('admin.orders.detail', [
            'order' => $order,
            'items' => $items
        ]);
    }

    // ✅ Update trạng thái
    public function updateStatus(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=admin/orders');
            exit;
        }

        $orderId = (int) $_POST['order_id'];
        $status = $_POST['status'];

        $db = getDB();
        $db->prepare("UPDATE orders SET status = ? WHERE order_id = ?")
            ->execute([$status, $orderId]);

        setFlash('success', 'Cập nhật trạng thái thành công');

        header('Location: ' . BASE_URL . '?act=admin/orders');
        exit;
    }

    // ✅ Dashboard (FIX lỗi render)
    public function dashboard()
    {
        requireAdmin();

        $db = getDB();

        $totalOrders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $revenue = $db->query("SELECT SUM(total) FROM orders")->fetchColumn();
        $totalProducts = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();

        $stmt = $db->query("
            SELECT 
                MONTH(order_date) as month,
                SUM(total) as revenue
            FROM orders
            GROUP BY MONTH(order_date)
            ORDER BY month
        ");
        $chartData = $stmt->fetchAll();

        view('admin.dashboard', [
            'totalOrders' => $totalOrders,
            'revenue' => $revenue,
            'totalProducts' => $totalProducts,
            'chartData' => $chartData
        ]);
    }
}
