<?php

class OrderController
{
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
            header('Location: ' . BASE_URL . 'profile');
            exit;
        }

        $fullname = trim($_POST['fullname'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        if ($fullname === '' || $phone === '' || $address === '') {
            setFlash('error', 'Vui lòng nhập đầy đủ thông tin.');
            header('Location: ' . BASE_URL . 'profile');
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
        header('Location: ' . BASE_URL . 'profile');
        exit;
    }
}