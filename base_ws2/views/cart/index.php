<?php ob_start(); ?>

<style>
  .payment-method-box .form-check {
    padding: 0.65rem 0.85rem;
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    margin-bottom: 0.5rem;
    transition: border-color 0.15s, background 0.15s;
  }
  .payment-method-box .form-check.is-selected {
    border-color: #198754;
    background: #f4fff8;
  }
  .transfer-qr-card {
    max-width: 320px;
    margin-left: auto;
    margin-right: auto;
  }
</style>

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

  <?php if (!empty($checkoutErrors ?? [])): ?>
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Có lỗi cần sửa:</div>
      <ul class="mb-0 ps-3">
        <?php foreach ($checkoutErrors as $err): ?>
          <li><?= htmlspecialchars((string) $err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
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

            <?php $co = is_array($checkoutOld ?? null) ? $checkoutOld : []; ?>
            <?php $pmOld = $co['payment_method'] ?? 'cash'; ?>

            <form id="checkout-form" method="post" action="<?= BASE_URL ?>?act=checkout" novalidate>

              <div class="mb-2">
                <label class="form-label" for="checkout-fullname">Họ tên</label>
                <input type="text" class="form-control" name="fullname" id="checkout-fullname" required
                       minlength="2" maxlength="120" autocomplete="name"
                       value="<?= htmlspecialchars((string) ($co['fullname'] ?? '')) ?>">
              </div>

              <div class="mb-2">
                <label class="form-label" for="checkout-phone">Số điện thoại</label>
                <input type="text" class="form-control" name="phone" id="checkout-phone" required
                       inputmode="tel" maxlength="16" autocomplete="tel"
                       placeholder="Ví dụ: 09xxxxxxxx"
                       value="<?= htmlspecialchars((string) ($co['phone'] ?? '')) ?>">
              </div>

              <div class="mb-3">
                <label class="form-label" for="checkout-address">Địa chỉ</label>
                <textarea class="form-control" name="address" id="checkout-address" rows="3" required
                          minlength="10" maxlength="500"><?= htmlspecialchars((string) ($co['address'] ?? '')) ?></textarea>
              </div>

              <div class="mb-3 payment-method-box">
                <label class="form-label fw-semibold">Phương thức thanh toán</label>

                <div class="form-check">
                  <input class="form-check-input" type="radio" name="payment_method" id="pay-cash" value="cash"
                         <?= $pmOld === 'cash' ? 'checked' : '' ?>>
                  <label class="form-check-label" for="pay-cash">
                    <strong>Tiền mặt (COD)</strong>
                    <span class="d-block small text-muted">Thanh toán khi nhận hàng</span>
                  </label>
                </div>

                <div class="form-check">
                  <input class="form-check-input" type="radio" name="payment_method" id="pay-transfer" value="transfer"
                         <?= $pmOld === 'transfer' ? 'checked' : '' ?>>
                  <label class="form-check-label" for="pay-transfer">
                    <strong>Chuyển khoản</strong>
                    <span class="d-block small text-muted">Quét mã QR để chuyển tiền trước khi giao hàng</span>
                  </label>
                </div>
              </div>

              <div id="transfer-qr-box" class="border rounded-3 p-3 mb-3 bg-light d-none">
                <p class="text-center small text-muted mb-2 mb-md-3">Quét mã để chuyển tiền đến</p>
                <div class="text-center mb-2">
                  <div class="fw-semibold">Tran Thi Loan</div>
                  <div class="text-secondary small">Techcombank</div>
                  <div class="font-monospace fs-5 mt-1">1907 5597 3510 12</div>
                </div>
                <div class="transfer-qr-card bg-white rounded-3 p-2 shadow-sm">
                  <img
                    src="<?= htmlspecialchars(asset('dist/assets/img/payment-vietqr.png'), ENT_QUOTES, 'UTF-8') ?>"
                    alt="QR chuyển khoản"
                    class="img-fluid w-100 rounded-2"
                    width="280"
                    height="280"
                  >
                </div>
                <p class="text-center small text-muted mt-2 mb-0">Vui lòng ghi nội dung chuyển khoản là mã đơn sau khi đặt hàng (nếu có).</p>
              </div>

              <button type="submit" class="btn btn-success w-100 fw-bold">
                🧾 Đặt hàng
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

<script>
(function () {
  var cash = document.getElementById('pay-cash');
  var transfer = document.getElementById('pay-transfer');
  var box = document.getElementById('transfer-qr-box');
  var wraps = document.querySelectorAll('.payment-method-box .form-check');
  if (!cash || !transfer || !box) return;
  function highlight() {
    wraps.forEach(function (w) { w.classList.remove('is-selected'); });
    if (cash.checked) cash.closest('.form-check').classList.add('is-selected');
    if (transfer.checked) transfer.closest('.form-check').classList.add('is-selected');
  }
  function sync() {
    box.classList.toggle('d-none', !transfer.checked);
    highlight();
  }
  cash.addEventListener('change', sync);
  transfer.addEventListener('change', sync);
  sync();
})();

(function () {
  var form = document.getElementById('checkout-form');
  if (!form) return;

  function digitsOnly(s) {
    return String(s || '').replace(/\D/g, '');
  }
  function normalizePhone(raw) {
    var d = digitsOnly(raw);
    if (d.indexOf('84') === 0 && d.length >= 10) {
      d = '0' + d.slice(2);
    }
    return d;
  }
  function validateCheckout() {
    var fullname = (document.getElementById('checkout-fullname') || {}).value || '';
    var phone = (document.getElementById('checkout-phone') || {}).value || '';
    var address = (document.getElementById('checkout-address') || {}).value || '';

    if (fullname.trim().length < 2) {
      alert('Họ tên phải có ít nhất 2 ký tự.');
      return false;
    }
    if (fullname.trim().length > 120) {
      alert('Họ tên không được vượt quá 120 ký tự.');
      return false;
    }
    if (!/[^\d\s]/.test(fullname.trim())) {
      alert('Họ tên phải có ít nhất một chữ cái (không chỉ số).');
      return false;
    }

    var p = normalizePhone(phone);
    if (!p) {
      alert('Vui lòng nhập số điện thoại.');
      return false;
    }
    if (!/^0[0-9]{9,10}$/.test(p)) {
      alert('Số điện thoại không hợp lệ (10–11 chữ số, bắt đầu bằng 0).');
      return false;
    }

    if (address.trim().length < 10) {
      alert('Địa chỉ giao hàng phải có ít nhất 10 ký tự.');
      return false;
    }
    if (address.trim().length > 500) {
      alert('Địa chỉ không được vượt quá 500 ký tự.');
      return false;
    }
    return true;
  }

  form.addEventListener('submit', function (e) {
    if (!validateCheckout()) {
      e.preventDefault();
      e.stopPropagation();
    }
  });
})();
</script>

<?php
$content = ob_get_clean();
view('layouts.StoreLayout', [
  'title' => 'Giỏ hàng',
  'content' => $content
]);
?>