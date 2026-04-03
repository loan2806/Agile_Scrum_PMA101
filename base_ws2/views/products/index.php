<?php ob_start(); ?>
<div class="container py-4">
  <h2 class="mb-3">Tất cả sản phẩm</h2>
  <?php if (!empty($flashSuccess)): ?><div class="alert alert-success"><?= htmlspecialchars($flashSuccess) ?></div><?php endif; ?>
  <?php if (!empty($flashError)): ?><div class="alert alert-danger"><?= htmlspecialchars($flashError) ?></div><?php endif; ?>
  <div class="card mb-3">
    <div class="card-body">
      <form class="row g-2" method="get" action="<?= BASE_URL ?>products">
        <div class="col-md-5"><input class="form-control" name="q" value="<?= htmlspecialchars($keyword ?? '') ?>" placeholder="Tìm theo tên sản phẩm"></div>
        <div class="col-md-5">
          <select class="form-select" name="category">
            <option value="0">Tất cả danh mục</option>
            <?php foreach (($categories ?? []) as $category): ?>
              <option value="<?= (int) $category['category_id'] ?>" <?= ((int) ($categoryId ?? 0) === (int) $category['category_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($category['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2 d-grid"><button class="btn btn-success">Lọc</button></div>
      </form>
    </div>
  </div>

  <div class="row g-3">
    <?php foreach (($products ?? []) as $product): ?>

      <div class="col-6 col-md-3">
        <div class="card product-card h-100">

          <!-- ẢNH -->
          <img
            src="<?= BASE_URL ?>public/dist/assets/img/<?= htmlspecialchars($product['image']) ?>"
            class="card-img-top"
            style="height:180px; object-fit:cover;"
            alt="<?= htmlspecialchars($product['name']) ?>">

          <div class="card-body">
            <h6><?= htmlspecialchars($product['name']) ?></h6>
            <p class="small text-muted mb-2"><?= htmlspecialchars($product['category_name'] ?? '') ?></p>
            <p class="price mb-2"><?= number_format((float) $product['price']) ?> đ</p>
            <a href="<?= BASE_URL ?>products/<?= (int) $product['product_id'] ?>" class="btn btn-sm btn-outline-success">
              Chi tiết
            </a>
          </div>

        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php
$content = ob_get_clean();
view('layouts.StoreLayout', ['title' => $title ?? 'Sản phẩm', 'content' => $content]);
?>