<?php

// Controller xử lý các chức năng liên quan đến xác thực (đăng nhập, đăng xuất)
class AuthController
{
    
    // Hiển thị form đăng nhập
    public function login()
    {
        // Nếu đã đăng nhập rồi thì chuyển về trang home
        if (isLoggedIn()) {
            header('Location: ' . BASE_URL . 'home');
            exit;   
        }

        // Lấy URL redirect nếu có (để quay lại trang đang xem sau khi đăng nhập)
        // Mặc định redirect về trang home
        $redirect = $_GET['redirect'] ?? BASE_URL . 'home';

        // Hiển thị view login
        view('auth.login', [
            'title' => 'Đăng nhập',
            'redirect' => $redirect,
        ]);
    }

    // Hiển thị form đăng ký
    public function register()
    {
        if (isLoggedIn()) {
            header('Location: ' . BASE_URL . 'home');
            exit;
        }

        view('auth.register', [
            'title' => 'Đăng ký tài khoản',
        ]);
    }

    // Xử lý đăng ký
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
        if ($fullname === '') {
            $errors[] = 'Vui lòng nhập họ tên';
        }
        if ($email === '') {
            $errors[] = 'Vui lòng nhập email';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }
        if (strlen($password) < 6) {
            $errors[] = 'Mật khẩu phải từ 6 ký tự';
        }
        if ($password !== $confirm) {
            $errors[] = 'Xác nhận mật khẩu không khớp';
        }

        $db = getDB();
        if ($db === null) {
            $errors[] = 'Không thể kết nối cơ sở dữ liệu';
        }

        if (empty($errors) && $db !== null) {
            $check = $db->prepare('SELECT user_id FROM users WHERE email = ? LIMIT 1');
            $check->execute([$email]);
            if ($check->fetch()) {
                $errors[] = 'Email đã tồn tại, vui lòng dùng email khác';
            }
        }

        if (!empty($errors)) {
            view('auth.register', [
                'title' => 'Đăng ký tài khoản',
                'errors' => $errors,
                'fullname' => $fullname,
                'email' => $email,
            ]);
            return;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $insert = $db->prepare('INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, ?, 1)');
        $insert->execute([$fullname, $email, $hash, 'user']);
        $userId = (int) $db->lastInsertId();

        $customerStmt = $db->prepare('INSERT INTO customers (fullname, email, phone, address, user_id) VALUES (?, ?, ?, ?, ?)');
        $customerStmt->execute([$fullname, $email, '', '', $userId]);

        $user = new User([
            'id' => $userId,
            'name' => $fullname,
            'email' => $email,
            'role' => 'user',
            'status' => 1,
        ]);
        loginUser($user);

        header('Location: ' . BASE_URL . 'home');
        exit;
    }

    // Xử lý đăng nhập (nhận dữ liệu từ form POST)
    public function checkLogin()
    {
        // Chỉ xử lý khi là POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        // Lấy dữ liệu từ form
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        // Mặc định redirect về trang home sau khi đăng nhập
        $redirect = $_POST['redirect'] ?? BASE_URL . 'home';

        // Validate dữ liệu đầu vào
        $errors = [];

        if (empty($email)) {
            $errors[] = 'Vui lòng nhập email';
        }

        if (empty($password)) {
            $errors[] = 'Vui lòng nhập mật khẩu';
        }

        // Nếu có lỗi validation thì quay lại form login
        if (!empty($errors)) {
            view('auth.login', [
                'title' => 'Đăng nhập',
                'errors' => $errors,
                'email' => $email,
                'redirect' => $redirect,
            ]);
            return;
        }

        $db = getDB();
        if ($db === null) {
            $errors[] = 'Không thể kết nối cơ sở dữ liệu';
            view('auth.login', [
                'title' => 'Đăng nhập',
                'errors' => $errors,
                'email' => $email,
                'redirect' => $redirect,
            ]);
            return;
        }

        $stmt = $db->prepare('SELECT user_id, username, email, password, role, status FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();

        $passwordMatched = false;
        if ($row) {
            $storedPassword = (string) $row['password'];
            $passwordMatched = password_verify($password, $storedPassword) || hash_equals($storedPassword, $password);
        }

        if (!$row || (int) $row['status'] !== 1 || !$passwordMatched) {
            $errors[] = 'Email hoặc mật khẩu không đúng';
            view('auth.login', [
                'title' => 'Đăng nhập',
                'errors' => $errors,
                'email' => $email,
                'redirect' => $redirect,
            ]);
            return;
        }

        $role = $row['role'] === 'admin' ? 'admin' : 'huong_dan_vien';
        $user = new User([
            'id' => (int) $row['user_id'],
            'name' => $row['username'],
            'email' => $row['email'],
            'role' => $role,
            'status' => (int) $row['status'],
        ]);

        // Đăng nhập thành công: lưu vào session
        loginUser($user);

        // Chuyển hướng về trang được yêu cầu hoặc trang chủ
        header('Location: ' . $redirect);
        exit;
    }

    // Xử lý đăng xuất
    public function logout()
    {
        // Xóa session và đăng xuất
        logoutUser();

        // Chuyển hướng về trang welcome
        header('Location: ' . BASE_URL . 'welcome');
        exit;
    }
}

