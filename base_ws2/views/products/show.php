<?php ob_start(); ?>

<div class="container py-5">

  <!-- ALERT -->
  <?php if (!empty($flashSuccess)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($flashSuccess) ?></div>
  <?php endif; ?>

  <?php if (!empty($flashError)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($flashError) ?></div>
  <?php endif; ?>

  <!-- PRODUCT DETAIL -->
  <div class="card shadow-lg border-0 rounded-4 mb-4">
    <div class="card-body p-4">
      <div class="row">

        <!-- LEFT: INFO -->
        <div class="col-md-7">
          <h2 class="fw-bold"><?= htmlspecialchars($product['name']) ?></h2>

          <p class="text-muted mb-2">
            <?= htmlspecialchars($product['category_name'] ?? '') ?>
          </p>

          <p><?= htmlspecialchars($product['description'] ?? '') ?></p>

          <h3 class="text-danger fw-bold">
            <?= number_format((float) $product['price']) ?> đ
          </h3>

          <p class="mt-2">
            <strong>Tồn kho:</strong> <?= (int) $product['stock'] ?>
          </p>
        </div>

        <!-- RIGHT: BUY -->
        <div class="col-md-5">
          <form method="post">

            <input type="hidden" name="product_id" value="<?= (int) $product['product_id'] ?>">

            <div class="mb-3">
              <label class="form-label fw-semibold">Số lượng</label>
              <input class="form-control rounded-3"
                     type="number"
                     name="quantity"
                     value="1"
                     min="1"
                     max="<?= (int) $product['stock'] ?>">
            </div>

            <div class="d-flex gap-2">

              <!-- Thêm vào giỏ -->
              <button class="btn btn-outline-success w-50"
                      formaction="<?= BASE_URL ?>cart/add">
                🛒 Thêm vào giỏ
              </button>

              <!-- Mua ngay -->
              <button class="btn btn-danger w-50 fw-bold"
                      formaction="<?= BASE_URL ?>checkout/buy-now">
                ⚡ Mua ngay
              </button>

            </div>

          </form>
        </div>

      </div>
    </div>
  </div>

  <!-- RELATED PRODUCTS -->
  <h4 class="mb-3 fw-bold">Sản phẩm liên quan</h4>

  <div class="row g-3">
    <?php foreach (($relatedProducts ?? []) as $item): ?>
      <div class="col-6 col-md-3">
        <div class="card h-100 shadow-sm border-0 rounded-3 product-card">

          <div class="card-body d-flex flex-column">
            <h6 class="fw-semibold">
              <?= htmlspecialchars($item['name']) ?>
            </h6>

            <p class="text-danger fw-bold mb-2">
              <?= number_format((float) $item['price']) ?> đ
            </p>

            <a class="btn btn-sm btn-outline-success mt-auto"
               href="<?= BASE_URL ?>products/<?= (int) $item['product_id'] ?>">
              Xem chi tiết
            </a>
          </div>

        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>

<?php
$content = ob_get_clean();
view('layouts.StoreLayout', [
  'title' => $title ?? 'Chi tiết sản phẩm',
  'content' => $content
]);
?>