<?php
require_once __DIR__ . '/../models/User.php';

class UserController
{
    // Danh sách user
    public function index()
    {
        $pdo = getDB();
        $stmt = $pdo->query("SELECT * FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        view('admin.users.index', compact('users'));
    }

    // Form thêm
    public function create()
    {
        view('admin.users.create');
    }

    // Lưu user
    public function store()
    {
        $data = $_POST;

        $pdo = getDB();
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password, role, status)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'],
            $data['status']
        ]);

        header("Location: " . BASE_URL . "admin/users");
        exit;
    }

    // Form sửa
    public function edit()
    {
        $id = $_GET['id'];
        $pdo = getDB();

        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id=?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        view('admin.users.edit', compact('user'));
    }

    // Update
    public function update()
    {
        $id = $_POST['id'];
        $data = $_POST;

        $pdo = getDB();
        $stmt = $pdo->prepare("
            UPDATE users 
            SET username=?, email=?, role=?, status=?
            WHERE user_id=?
        ");

        $stmt->execute([
            $data['username'],
            $data['email'],
            $data['role'],
            $data['status'],
            $id
        ]);

        header("Location: " . BASE_URL . "admin/users");
        exit;
    }

    // Xóa
    public function delete()
    {
        $id = $_GET['id'];
    
        $pdo = getDB();
    
        // ❗ Xóa bảng phụ trước
        $stmt = $pdo->prepare("DELETE FROM customers WHERE user_id=?");
        $stmt->execute([$id]);
    
        // ❗ Sau đó xóa user
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id=?");
        $stmt->execute([$id]);
    
        header("Location: " . BASE_URL . "admin/users");
        exit;
    }}