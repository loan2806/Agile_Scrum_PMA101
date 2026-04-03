<?php ob_start(); ?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Giỏ hàng của bạn</h4>
    <a href="<?= BASE_URL ?>?act=products" class="btn btn-outline-success">Tiếp tục mua hàng</a>
  </div>

  <!-- ALERT -->
  <?php if (!empty($flashSuccess)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($flashSuccess) ?></div>
  <?php endif; ?>

  <?php if (!empty($flashError)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($flashError) ?></div>
  <?php endif; ?>

  <?php if (empty($items)): ?>
    <div class="alert alert-info">Giỏ hàng đang rỗng.</div>
  <?php else: ?>

    <!-- DANH SÁCH SẢN PHẨM -->
    <div class="card mb-3 product-card shadow-sm">
      <div class="card-body p-0">
        <table class="table mb-0">
          <thead>
            <tr>
              <th>Sản phẩm</th>
              <th>Đơn giá</th>
              <th style="width: 180px;">Số lượng</th>
              <th>Tạm tính</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $item): ?>
              <tr>
                <td>
                  <div class="fw-semibold"><?= htmlspecialchars($item['name']) ?></div>
                </td>

                <td><?= number_format($item['price']) ?> đ</td>

                <td>
                  <form method="post" action="<?= BASE_URL ?>?act=cart/update" class="d-flex gap-2">
                    <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                    <input type="number"
                           name="quantity"
                           min="1"
                           max="<?= (int) $item['stock'] ?>"
                           value="<?= (int) $item['quantity'] ?>"
                           class="form-control form-control-sm">
                    <button class="btn btn-sm btn-primary">Cập nhật</button>
                  </form>
                </td>

                <td><?= number_format($item['subtotal']) ?> đ</td>

                <td>
                  <form method="post" action="<?= BASE_URL ?>?act=cart/remove">
                    <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                    <button class="btn btn-sm btn-outline-danger">Xóa</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- CHECKOUT -->
    <div class="row g-3">

      <!-- FORM -->
      <div class="col-md-7">
        <div class="card product-card shadow-sm">
          <div class="card-body">
            <h5 class="mb-3">Thông tin giao hàng</h5>

            <form method="post" action="<?= BASE_URL ?>?act=checkout">

              <div class="mb-2">
                <label class="form-label">Họ tên</label>
                <input type="text" class="form-control" name="fullname" required>
              </div>

              <div class="mb-2">
                <label class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" name="phone" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Địa chỉ</label>
                <textarea class="form-control" name="address" rows="3" required></textarea>
              </div>

              <button class="btn btn-success w-100 fw-bold">
                🧾 Đặt hàng (COD)
              </button>

            </form>
          </div>
        </div>
      </div>

      <!-- SUMMARY -->
      <div class="col-md-5">
        <div class="card product-card shadow-sm">
          <div class="card-body">
            <h5>Tóm tắt đơn hàng</h5>

            <p class="mb-1">
              Số mặt hàng:
              <strong><?= count($items) ?></strong>
            </p>

            <p class="mb-1">
              Tổng sản phẩm:
              <strong><?= (int) ($cartCount ?? 0) ?></strong>
            </p>

            <p class="mb-3">
              Phí vận chuyển:
              <strong>0 đ</strong>
            </p>

            <h4 class="text-danger fw-bold">
              Tổng cộng: <?= number_format((float) $total) ?> đ
            </h4>

          </div>
        </div>
      </div>

    </div>

  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
view('layouts.StoreLayout', [
  'title' => 'Giỏ hàng',
  'content' => $content
]);
?>