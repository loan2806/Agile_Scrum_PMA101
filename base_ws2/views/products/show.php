<?php ob_start(); ?>
<div class="container py-4">
  <?php if (!empty($flashSuccess)): ?><div class="alert alert-success"><?= htmlspecialchars($flashSuccess) ?></div><?php endif; ?>
  <?php if (!empty($flashError)): ?><div class="alert alert-danger"><?= htmlspecialchars($flashError) ?></div><?php endif; ?>
  <div class="card mb-4">
    <div class="card-body">
      <div class="row">
        <div class="col-md-7">
          <h2><?= htmlspecialchars($product['name']) ?></h2>
          <p class="text-muted mb-2"><?= htmlspecialchars($product['category_name'] ?? '') ?></p>
          <p><?= htmlspecialchars($product['description'] ?? '') ?></p>
          <h4 class="price"><?= number_format((float) $product['price']) ?> đ</h4>
          <p>Tồn kho: <?= (int) $product['stock'] ?></p>
        </div>
        <div class="col-md-5">
          <form method="post" action="<?= BASE_URL ?>cart/add">
            <input type="hidden" name="product_id" value="<?= (int) $product['product_id'] ?>">
            <div class="mb-3">
              <label class="form-label">Số lượng</label>
              <input class="form-control" type="number" name="quantity" value="1" min="1" max="<?= (int) $product['stock'] ?>">
            </div>
            <button class="btn btn-success">Thêm vào giỏ</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <h4 class="section-title">Sản phẩm liên quan</h4>
  <div class="row g-3">
    <?php foreach (($relatedProducts ?? []) as $item): ?>
      <div class="col-6 col-md-3">
        <div class="card product-card h-100">
          <div class="card-body">
            <h6><?= htmlspecialchars($item['name']) ?></h6>
            <p class="price mb-2"><?= number_format((float) $item['price']) ?> đ</p>
            <a class="btn btn-sm btn-outline-success" href="<?= BASE_URL ?>products/<?= (int) $item['product_id'] ?>">Xem</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php
$content = ob_get_clean();
view('layouts.StoreLayout', ['title' => $title ?? 'Chi tiết sản phẩm', 'content' => $content]);
?>
