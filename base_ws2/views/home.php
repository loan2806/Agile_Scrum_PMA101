<?php ob_start(); ?>
<div class="container py-4">
  <section class="hero p-4 p-md-5 mb-4">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h1 class="mb-2">Trái cây chất lượng cao</h1>
        <p class="mb-3">Đa dạng trái cây nội địa, nhập khẩu và hộp quà tặng cho mọi dịp đặc biệt.</p>
        <a href="<?= BASE_URL ?>products" class="btn btn-light">Xem tất cả sản phẩm</a>
      </div>
      <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="<?= BASE_URL ?>cart" class="btn btn-warning">Giỏ hàng (<?= (int) ($cartCount ?? 0) ?>)</a>
      </div>
    </div>
  </section>
  <div class="store-note p-3 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <span><strong>Ưu đãi:</strong> Giảm 25.000đ phí ship cho đơn trên 600.000đ.</span>
    <a href="<?= BASE_URL ?>products" class="btn btn-sm btn-outline-success">Mua ngay</a>
  </div>

  <?php if (!empty($flashSuccess)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($flashSuccess) ?></div>
  <?php endif; ?>
  <?php if (!empty($flashError)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($flashError) ?></div>
  <?php endif; ?>

  <h3 class="section-title">Danh mục nổi bật</h3>
  <div class="row g-3">
    <?php foreach (($categories ?? []) as $category): ?>
      <div class="col-6 col-md-3">
        <a class="card featured-category text-decoration-none p-3 h-100" href="<?= BASE_URL ?>products?category=<?= (int) $category['category_id'] ?>">
          <strong class="text-dark"><?= htmlspecialchars($category['name']) ?></strong>
        </a>
      </div>
    <?php endforeach; ?>
  </div>

  <?php
    $sections = [
      'Trái ngon hôm nay' => $featuredProducts ?? [],
      'Trái cây Việt Nam' => $vietnamProducts ?? [],
      'Trái cây nhập khẩu' => $importedProducts ?? [],
    ];
  ?>
  <?php foreach ($sections as $label => $products): ?>
    <h3 class="section-title"><?= $label ?></h3>
    <div class="row g-3">
      <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
          <div class="col-6 col-md-3">
            <div class="card product-card h-100">
              <div class="card-body">
                <h6 class="mb-1"><?= htmlspecialchars($product['name']) ?></h6>
                <p class="small text-muted mb-2"><?= htmlspecialchars($product['description'] ?? '') ?></p>
                <p class="price mb-2"><?= number_format((float) $product['price']) ?> đ</p>
                <a href="<?= BASE_URL ?>products/<?= (int) $product['product_id'] ?>" class="btn btn-sm btn-outline-success">Xem chi tiết</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12"><div class="alert alert-light border">Chưa có dữ liệu.</div></div>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>
<?php
$content = ob_get_clean();
view('layouts.StoreLayout', ['title' => 'Fruit Shop - Trang chủ', 'content' => $content]);
?>