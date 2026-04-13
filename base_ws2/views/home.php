<?php ob_start(); ?>

<div class="container py-4">

  <!-- 🔥 BANNER CAROUSEL -->
  <div id="homeCarousel" class="carousel slide mb-4" data-bs-ride="carousel">

    <div class="carousel-inner rounded-4 shadow">

      <div class="carousel-item active">
        <img src="https://theme.hstatic.net/200000377165/1001286359/14/slide_1_mb.jpg?v=476"
          class="d-block w-100"
          style="height:300px; object-fit:cover;">
        <div class="carousel-caption text-start">
          <h3>Trái cây tươi mỗi ngày</h3>
          <p>Chất lượng cao - Giá tốt</p>
        </div>
      </div>

      <div class="carousel-item">
        <img src="https://theme.hstatic.net/200000377165/1001286359/14/slide_2_mb.jpg?v=476"
          class="d-block w-100"
          style="height:300px; object-fit:cover;">
        <div class="carousel-caption text-start">
          <h3>Ưu đãi cực lớn</h3>
          <p>Giảm giá mỗi ngày</p>
        </div>
      </div>

      <div class="carousel-item">
        <img src="https://theme.hstatic.net/200000377165/1001286359/14/slide_3_mb.jpg?v=476"
          class="d-block w-100"
          style="height:300px; object-fit:cover;">
        <div class="carousel-caption text-start">
          <h3>Trái cây nhập khẩu</h3>
          <p>Táo Mỹ, nho Úc, cherry</p>
        </div>
      </div>

    </div>

    <!-- Nút điều hướng -->
    <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>

  </div>

  <!-- HERO -->
  <section class="hero p-4 mb-4 rounded-4 shadow-sm bg-success text-white">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h1>🍎 Fruit Shop</h1>
        <p>Trái cây sạch - giao nhanh - giá tốt</p>
        <a href="<?= BASE_URL ?>products" class="btn btn-light">Xem sản phẩm</a>
      </div>
      <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="<?= BASE_URL ?>cart" class="btn btn-warning">
          🛒 Giỏ hàng (<?= (int) ($cartCount ?? 0) ?>)
        </a>
      </div>
    </div>
  </section>

  <!-- ƯU ĐÃI -->
  <div class="alert alert-warning d-flex justify-content-between align-items-center flex-wrap">
    <span><strong>🔥 Ưu đãi:</strong> Giảm 25.000đ phí ship cho đơn > 600k</span>
    <a href="<?= BASE_URL ?>products" class="btn btn-sm btn-success">Mua ngay</a>
  </div>

  <!-- ALERT -->
  <?php if (!empty($flashSuccess)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($flashSuccess) ?></div>
  <?php endif; ?>

  <?php if (!empty($flashError)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($flashError) ?></div>
  <?php endif; ?>

  <!-- DANH MỤC -->
  <h3 class="mb-3">Danh mục nổi bật</h3>
  <div class="row g-3">
    <?php foreach (($categories ?? []) as $category): ?>
      <div class="col-6 col-md-3">
        <a class="card p-3 text-center h-100 shadow-sm border-0 rounded-3 hover-card"
          href="<?= BASE_URL ?>products?category=<?= (int) $category['category_id'] ?>">
          <strong><?= htmlspecialchars($category['name']) ?></strong>
        </a>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- PRODUCTS -->
  <?php
  $sections = [
    '🔥 Trái ngon hôm nay' => $featuredProducts ?? [],
    '🇻🇳 Trái cây Việt Nam' => $vietnamProducts ?? [],
    '🌍 Trái cây nhập khẩu' => $importedProducts ?? [],
  ];
  ?>

  <?php foreach ($sections as $label => $products): ?>
    <h3 class="mt-4 mb-3"><?= $label ?></h3>

    <div class="row g-3">
      <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
          <div class="col-6 col-md-3">
            <div class="card h-100 shadow-sm border-0 rounded-3 product-card">

              <img src="<?= BASE_URL ?>public/dist/assets/img/<?= $product['image'] ?>"
                class="card-img-top"
                style="height:180px; object-fit:cover;">

              <div class="card-body d-flex flex-column">
                <h6><?= htmlspecialchars($product['name']) ?></h6>

                <p class="small text-muted">
                  <?= htmlspecialchars($product['description'] ?? '') ?>
                </p>

                <p class="text-danger fw-bold">
                  <?= number_format((float) $product['price']) ?> đ
                </p>

                <a href="<?= BASE_URL ?>products/<?= (int) $product['product_id'] ?>"
                  class="btn btn-sm btn-outline-success mt-auto">
                  Xem chi tiết
                </a>
              </div>

            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="alert alert-light border">Chưa có dữ liệu.</div>
        </div>
      <?php endif; ?>
    </div>

  <?php endforeach; ?>

</div>

<!-- 🔥 CSS đẹp hơn -->
<style>
  .hover-card:hover {
    transform: translateY(-5px);
    transition: 0.3s;
  }

  .product-card:hover {
    transform: scale(1.03);
    transition: 0.3s;
  }

  .hover-card {
    text-decoration: none;
    color: inherit;
  }

  .hover-card:hover {
    transform: translateY(-5px);
    transition: 0.3s;
    color: #198754;
    /* xanh bootstrap */
  }
</style>

<?php
$content = ob_get_clean();
view('layouts.StoreLayout', [
  'title' => 'Fruit Shop - Trang chủ',
  'content' => $content
]);
?>