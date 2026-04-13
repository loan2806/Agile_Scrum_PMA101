<?php ob_start(); ?>
<style>
  .orders-wrap {
    max-width: 1000px;
  }

  .orders-title {
    font-weight: 700;
    letter-spacing: .2px;
    color: #1f2937;
  }

  .orders-subtitle {
    color: #6b7280;
    font-size: .95rem;
  }

  .orders-card {
    border: 0;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(17, 24, 39, .08);
  }

  .orders-table thead th {
    background: #f8fafc;
    color: #374151;
    font-weight: 600;
    border-bottom: 1px solid #e5e7eb;
    white-space: nowrap;
  }

  .orders-table tbody tr:hover {
    background: #f9fbff;
  }

  .orders-table td,
  .orders-table th {
    padding: .9rem 1rem;
    vertical-align: middle;
  }

  .order-id {
    font-weight: 700;
    color: #111827;
  }

  .order-date {
    color: #4b5563;
  }

  .order-total {
    font-weight: 700;
    color: #dc2626;
    white-space: nowrap;
  }

  .status-badge {
    display: inline-block;
    padding: .35rem .65rem;
    border-radius: 999px;
    font-size: .78rem;
    font-weight: 600;
    text-transform: capitalize;
  }

  .status-pending { background: #fff7ed; color: #c2410c; }
  .status-processing { background: #eff6ff; color: #1d4ed8; }
  .status-shipped { background: #ecfeff; color: #0e7490; }
  .status-completed { background: #ecfdf5; color: #047857; }
  .status-cancelled { background: #fef2f2; color: #b91c1c; }

  .empty-orders {
    padding: 2rem 1rem;
    color: #6b7280;
  }
</style>

<div class="container py-4">
  <div class="orders-wrap mx-auto">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-3">
      <div>
        <h2 class="mb-1 orders-title">Don hang cua toi</h2>
        <div class="orders-subtitle">Theo doi tat ca don hang ban da dat.</div>
      </div>
    </div>

    <?php if (!empty($flashSuccess)): ?><div class="alert alert-success border-0 shadow-sm"><?= htmlspecialchars($flashSuccess) ?></div><?php endif; ?>
    <?php if (!empty($flashError)): ?><div class="alert alert-danger border-0 shadow-sm"><?= htmlspecialchars($flashError) ?></div><?php endif; ?>

    <div class="card orders-card">
    <div class="card-body p-0">
      <div class="table-responsive">
      <table class="table orders-table mb-0">
        <thead>
          <tr>
            <th>Mã đơn</th>
            <th>Ngày đặt</th>
            <th>Trạng thái</th>
            <th>Tổng tiền</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $order): ?>
              <?php
                $rawStatus = strtolower((string) ($order['status'] ?? 'pending'));
                $statusMap = [
                  'pending' => 'Cho xac nhan',
                  'processing' => 'Dang xu ly',
                  'shipped' => 'Dang giao',
                  'completed' => 'Hoan thanh',
                  'cancelled' => 'Da huy',
                ];
                $statusLabel = $statusMap[$rawStatus] ?? ucfirst($rawStatus);
              ?>
              <tr>
                <td class="order-id">#<?= (int) $order['order_id'] ?></td>
                <td class="order-date"><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) $order['order_date']))) ?></td>
                <td><span class="status-badge status-<?= htmlspecialchars($rawStatus) ?>"><?= htmlspecialchars($statusLabel) ?></span></td>
                <td class="order-total"><?= number_format((float) $order['total']) ?> đ</td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="4" class="text-center empty-orders">Ban chua co don hang nao.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
      </div>
    </div>
  </div>
  </div>
</div>
<?php
$content = ob_get_clean();
view('layouts.StoreLayout', ['title' => $title ?? 'Đơn hàng của tôi', 'content' => $content]);
?>
