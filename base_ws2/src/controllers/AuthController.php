<?php

class AuthController
{
    public function login()
    {
        if (isLoggedIn()) {
            header('Location: ' . BASE_URL . 'home');
            exit;
        }

        $redirect = $_GET['redirect'] ?? BASE_URL . 'home';

        view('auth.login', [
            'title' => 'Đăng nhập',
            'redirect' => $redirect,
        ]);
    }

    public function checkLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
    
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
    
        $errors = [];
    
        if (empty($email)) $errors[] = 'Vui lòng nhập email';
        if (empty($password)) $errors[] = 'Vui lòng nhập mật khẩu';
    
        if (!empty($errors)) {
            view('auth.login', compact('errors', 'email'));
            return;
        }
    
        $db = getDB();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // ❌ Không tồn tại email
        if (!$row) {
            $errors[] = 'Email không tồn tại';
            view('auth.login', compact('errors', 'email'));
            return;
        }
    
        // ❌ Sai mật khẩu
        $passwordMatched = password_verify($password, $row['password']) 
            || $password === $row['password'];
    
        if (!$passwordMatched) {
            $errors[] = 'Mật khẩu không đúng';
            view('auth.login', compact('errors', 'email'));
            return;
        }
    
        // 🔒 BỊ KHÓA
        if ((int)$row['status'] !== 1) {
            $errors[] = 'Tài khoản đã bị khóa';
            view('auth.login', compact('errors', 'email'));
            return;
        }
    
        // ✅ LOGIN THÀNH CÔNG
        $user = new User([
            'id' => (int)$row['user_id'],
            'name' => $row['username'],
            'email' => $row['email'],
            'role' => $row['role'],
            'status' => (int)$row['status'],
        ]);
    
        loginUser($user);
    
        // Redirect theo role
        if ($row['role'] === 'admin') {
            header('Location: ' . BASE_URL . 'admin/dashboard');
        } else {
            header('Location: ' . BASE_URL . 'home');
        }
        exit;
    }    public function register()
    {
        if (isLoggedIn()) {
            header('Location: ' . BASE_URL . 'home');
            exit;
        }

        view('auth.register', [
            'title' => 'Đăng ký tài khoản',
        ]);
    }

    public function storeRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'register');
            exit;
        }

        $fullname = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirmation'] ?? '';

        $errors = [];

        if ($fullname === '') $errors[] = 'Vui lòng nhập họ tên';
        if ($email === '') $errors[] = 'Vui lòng nhập email';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ';
        if (strlen($password) < 6) $errors[] = 'Mật khẩu phải từ 6 ký tự';
        if ($password !== $confirm) $errors[] = 'Xác nhận mật khẩu không khớp';

        $db = getDB();

        if (empty($errors)) {
            $check = $db->prepare('SELECT user_id FROM users WHERE email = ?');
            $check->execute([$email]);
            if ($check->fetch()) {
                $errors[] = 'Email đã tồn tại';
            }
        }

        if (!empty($errors)) {
            view('auth.register', compact('errors', 'fullname', 'email'));
            return;
        }

        // ✅ HASH PASSWORD
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $db->prepare('INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, ?, 1)');
        $stmt->execute([$fullname, $email, $hash, 'user']);

        $userId = $db->lastInsertId();

        $db->prepare('INSERT INTO customers (fullname, email, phone, address, user_id) VALUES (?, ?, ?, ?, ?)')
           ->execute([$fullname, $email, '', '', $userId]);

        header('Location: ' . BASE_URL . 'login');
        exit;
    }

    public function logout()
    {
        logoutUser();
        header('Location: ' . BASE_URL . 'welcome');
        exit;
    }
}