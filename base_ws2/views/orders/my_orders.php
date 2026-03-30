<?php ob_start(); ?>
<div class="container py-4">
  <h2 class="mb-3">Đơn hàng của tôi</h2>
  <?php if (!empty($flashSuccess)): ?><div class="alert alert-success"><?= htmlspecialchars($flashSuccess) ?></div><?php endif; ?>
  <?php if (!empty($flashError)): ?><div class="alert alert-danger"><?= htmlspecialchars($flashError) ?></div><?php endif; ?>
  <div class="card">
    <div class="card-body p-0">
      <table class="table mb-0">
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
              <tr>
                <td>#<?= (int) $order['order_id'] ?></td>
                <td><?= htmlspecialchars($order['order_date']) ?></td>
                <td><?= htmlspecialchars($order['status']) ?></td>
                <td><?= number_format((float) $order['total']) ?> đ</td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="4" class="text-center">Bạn chưa có đơn hàng nào.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
view('layouts.StoreLayout', ['title' => $title ?? 'Đơn hàng của tôi', 'content' => $content]);
?>
