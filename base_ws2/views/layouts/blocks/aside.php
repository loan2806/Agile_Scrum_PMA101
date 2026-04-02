<!--begin::Sidebar-->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <!--begin::Sidebar Brand-->
  <div class="sidebar-brand">
    <a href="<?= BASE_URL . 'home' ?>" class="brand-link">
      <img
        src="<?= asset('dist/assets/img/AdminLTELogo.png') ?>"
        alt="Logo"
        class="brand-image opacity-75 shadow"
      />
      <span class="brand-text fw-light">Fruit Shop</span>
    </a>
  </div>
  <!--end::Sidebar Brand-->

  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
        
        <!-- Trang chủ -->
        <li class="nav-item">
          <a href="<?= BASE_URL . 'home' ?>" class="nav-link">
            <i class="nav-icon bi bi-speedometer"></i>
            <p>Sản phẩm (khách)</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= BASE_URL . 'cart' ?>" class="nav-link">
            <i class="nav-icon bi bi-cart3"></i>
            <p>Gio hang</p>
          </a>
        </li>

        <?php if (isAdmin()): ?>
          <li class="nav-header">QUẢN TRỊ</li>
          <li class="nav-item">
            <a href="<?= BASE_URL . 'admin/products' ?>" class="nav-link">
              <i class="nav-icon bi bi-box-seam"></i>
              <p>Sản phẩm (admin)</p>
            </a>
          </li>
        <?php endif; ?>

        <!-- Hệ thống -->
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