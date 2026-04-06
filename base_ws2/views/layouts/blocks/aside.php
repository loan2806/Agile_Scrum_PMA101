<?php $act = $_GET['act'] ?? ''; ?>

<!--begin::Sidebar-->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">

  <!-- Logo -->
  <div class="sidebar-brand">
    <a href="<?= BASE_URL . 'home' ?>" class="brand-link">
      <img src="<?= asset('dist/assets/img/AdminLTELogo.png') ?>" class="brand-image opacity-75 shadow">
      <span class="brand-text fw-light">Fruit Shop</span>
    </a>
  </div>

  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <ul class="nav sidebar-menu flex-column">

        <!-- USER -->

        <!-- ADMIN -->
        <?php if (isAdmin()): ?>
          <li class="nav-header">QUẢN TRỊ</li>

          <li class="nav-item">
            <a href="<?= BASE_URL . 'admin/dashboard' ?>"
               class="nav-link <?= $act === 'admin/dashboard' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-speedometer2"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= BASE_URL . 'admin/users' ?>"
               class="nav-link <?= $act === 'admin/users' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-people"></i>
              <p>Quản lý người dùng</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?= BASE_URL . 'admin/products' ?>"
               class="nav-link <?= $act === 'admin/products' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-box-seam"></i>
              <p>Quản lý sản phẩm</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?= BASE_URL . 'admin/categories' ?>"
               class="nav-link <?= $act === 'admin/categories' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-tags"></i>
              <p>Danh mục</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?= BASE_URL . 'admin/orders' ?>"
               class="nav-link <?= $act === 'admin/orders' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-bag-check"></i>
              <p>Quản lý đơn hàng</p>
            </a>
          </li>

        <?php endif; ?>

        <!-- SYSTEM -->
        <li class="nav-header">HỆ THỐNG</li>

        <li class="nav-item">
          <a href="<?= BASE_URL . 'logout' ?>" class="nav-link">
            <i class="nav-icon bi bi-box-arrow-right"></i>
            <p>Đăng xuất</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>
<!--end::Sidebar-->