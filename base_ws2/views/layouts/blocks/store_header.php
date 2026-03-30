<?php $currentUser = getCurrentUser(); ?>
<header class="store-header">
  <div class="topbar">
    <div class="container d-flex justify-content-between align-items-center">
      <span>Trái cây tươi chất lượng cao</span>
      <div class="d-flex gap-3">
        <span><i class="bi bi-telephone"></i> 0865 660 775</span>
        <?php if ($currentUser): ?>
          <span>Xin chào, <?= htmlspecialchars($currentUser->name) ?></span>
          <a class="text-decoration-none text-dark" href="<?= BASE_URL ?>profile">Tài khoản</a>
          <a class="text-decoration-none text-dark" href="<?= BASE_URL ?>logout">Đăng xuất</a>
        <?php else: ?>
          <a class="text-decoration-none text-dark" href="<?= BASE_URL ?>login">Đăng nhập</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="mainbar">
    <div class="container d-flex align-items-center justify-content-between gap-3 py-3">
      <a class="store-logo text-decoration-none" href="<?= BASE_URL ?>home">Fruit Shop</a>
      <form class="search-form" action="<?= BASE_URL ?>products" method="get">
        <input type="text" name="q" placeholder="Tìm trái cây..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        <button type="submit"><i class="bi bi-search"></i></button>
      </form>
      <a href="<?= BASE_URL ?>cart" class="cart-link text-decoration-none">
        <i class="bi bi-cart3"></i>
        <span>Giỏ hàng</span>
        <strong><?= cartCount() ?></strong>
      </a>
    </div>
  </div>
  <nav class="menu-bar">
    <div class="container">
      <ul class="menu-list">
        <li><a href="<?= BASE_URL ?>home">Trang chủ</a></li>
        <li><a href="<?= BASE_URL ?>products?group=featured">Trái ngon hôm nay</a></li>
        <li><a href="<?= BASE_URL ?>products?category=1">Trái cây Việt Nam</a></li>
        <li><a href="<?= BASE_URL ?>products?category=2">Trái cây nhập khẩu</a></li>
        <li><a href="<?= BASE_URL ?>products">Tất cả sản phẩm</a></li>
        <?php if ($currentUser): ?>
          <li><a href="<?= BASE_URL ?>my-orders">Đơn hàng của tôi</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </nav>
</header>
