<?php ob_start(); ?>

<div class="container-fluid py-3 px-4">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0 fw-bold">📦 Đơn hàng</h6>
  </div>

  <!-- Grid -->
  <div class="row g-2">
    <?php foreach ($orders as $o): ?>
      <div class="col-12 col-md-6 col-lg-4 col-xl-3">

        <div class="card border-0 shadow-sm h-100 order-card">

          <div class="card-body py-2 px-3 d-flex flex-column justify-content-between">

            <!-- Top -->
            <div>

              <div class="d-flex justify-content-between align-items-center mb-1">
                <strong>#<?= $o['order_id'] ?></strong>

                <?php
                $statusClass = [
                  'Đang xử lý' => 'secondary',
                  'Đang giao' => 'warning',
                  'Đã giao' => 'success',
                  'Đã hủy' => 'danger'
                ];
                ?>
                <span class="badge bg-<?= $statusClass[$o['status']] ?? 'secondary' ?>">
                  <?= $o['status'] ?>
                </span>
              </div>

              <div class="small text-muted mb-1">
                👤 <?= $o['fullname'] ?>
              </div>

              <div class="small text-muted mb-1">
                📅 <?= date('d/m/Y', strtotime($o['order_date'])) ?>
              </div>

              <div class="fw-bold text-danger mb-2">
                <?= number_format($o['total']) ?> đ
              </div>

            </div>

            <!-- Action -->
            <form method="post" action="<?= BASE_URL ?>?act=admin/orders/update">
              <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">

              <div class="d-flex gap-1 align-items-center">

                <select name="status" class="form-select form-select-sm">
                  <option <?= $o['status'] == 'Đang xử lý' ? 'selected' : '' ?>>Đang xử lý</option>
                  <option <?= $o['status'] == 'Đang giao' ? 'selected' : '' ?>>Đang giao</option>
                  <option <?= $o['status'] == 'Đã giao' ? 'selected' : '' ?>>Đã giao</option>
                  <option <?= $o['status'] == 'Đã hủy' ? 'selected' : '' ?>>Đã hủy</option>
                </select>
                <a href="<?= BASE_URL ?>?act=admin/order-detail&id=<?= $o['order_id'] ?>"
                  class="btn btn-sm btn-primary">
                  👁
                </a>
                <button class="btn btn-sm btn-success px-2">
                  ✔
                </button>
              </div>
            </form>

          </div>

        </div>

      </div>
    <?php endforeach; ?>
  </div>

</div>

<!-- CSS -->
<style>
  body {
    background: #f5f5f5;
  }

  .order-card {
    border-radius: 10px;
    transition: 0.2s;
  }

  .order-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
  }
</style>

<?php
$content = ob_get_clean();
view('layouts.AdminLayout', [
  'title' => 'Quản lý đơn hàng',
  'content' => $content
]);
?>