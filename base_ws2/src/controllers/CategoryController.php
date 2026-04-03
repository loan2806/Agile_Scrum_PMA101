<?php

class CategoryController
{
    private function requireAdminAccess()
    {
        requireLogin();
        if (!isAdmin()) {
            header('Location: ' . BASE_URL . 'home');
            exit;
        }
    }

    // ================= LIST =================
    public function index()
    {
        $this->requireAdminAccess();
        $db = getDB();
        $categories = $db->query("SELECT * FROM categories ORDER BY category_id DESC")->fetchAll();

        $content = view('admin.categories.index', [
            'categories' => $categories
        ], true);

        view('layouts.dashboard', [
            'title' => 'Danh mục',
            'content' => $content
        ]);
    }

    // ================= CREATE =================
    public function store()
    {
        $this->requireAdminAccess();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "admin/categories");
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            setFlash('error', 'Tên danh mục không được để trống');
            header("Location: " . BASE_URL . "admin/categories");
            exit;
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO categories(name, slug) VALUES (?, ?)");
        $stmt->execute([$name, $slug]);

        setFlash('success', 'Thêm danh mục thành công');
        header("Location: " . BASE_URL . "admin/categories");
        exit;
    }

    // ================= EDIT =================public function edit()
    public function edit()
    {
        $this->requireAdminAccess();
        $id = (int)($_GET['id'] ?? 0);
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM categories WHERE category_id=?");
        $stmt->execute([$id]);
        $category = $stmt->fetch();
    
        if (!$category) {
            setFlash('error','Danh mục không tồn tại');
            header("Location: " . BASE_URL . "admin/categories"); exit;
        }
    
        $content = view('admin.categories.edit', ['category'=>$category], true);
        view('layouts.dashboard', ['title'=>'Chỉnh sửa danh mục', 'content'=>$content]);
    }
    
    public function update()
    {
        $this->requireAdminAccess();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location:" . BASE_URL . "admin/categories"); exit;
        }
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        if ($id<=0 || $name==='') {
            setFlash('error','Dữ liệu không hợp lệ');
            header("Location:" . BASE_URL . "admin/categories/edit&id=$id"); exit;
        }
        $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/','-',$name));
        $db = getDB();
        $stmt = $db->prepare("UPDATE categories SET name=?, slug=? WHERE category_id=?");
        $stmt->execute([$name,$slug,$id]);
    
        setFlash('success','Cập nhật danh mục thành công');
        header("Location:".BASE_URL."admin/categories"); exit;
    }
    // ================= DELETE =================
    public function delete()
    {
        $this->requireAdminAccess();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "admin/categories");
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            header("Location: " . BASE_URL . "admin/categories");
            exit;
        }

        $db = getDB();
        // Kiểm tra sản phẩm liên kết
        $count = $db->prepare("SELECT COUNT(*) FROM products WHERE category_id=?");
        $count->execute([$id]);
        if ($count->fetchColumn() > 0) {
            setFlash('error', 'Danh mục này đang có sản phẩm, không thể xóa!');
            header("Location: " . BASE_URL . "admin/categories");
            exit;
        }

        $db->prepare("DELETE FROM categories WHERE category_id=?")->execute([$id]);
        setFlash('success', 'Xóa danh mục thành công');
        header("Location: " . BASE_URL . "admin/categories");
        exit;
    }
}