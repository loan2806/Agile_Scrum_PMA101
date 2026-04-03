<?php ob_start(); ?>

<div class="container py-4">
  <h3>Chi tiết đơn hàng #<?= $order['order_id'] ?></h3>

  <p><strong>Trạng thái:</strong> <?= $order['status'] ?></p>
  <p><strong>Ngày:</strong> <?= $order['order_date'] ?></p>

  <table class="table table-bordered mt-3">
    <thead>
      <tr>
        <th>Sản phẩm</th>
        <th>Số lượng</th>
        <th>Giá</th>
        <th>Tổng</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
        <tr>
          <td><?= $item['name'] ?></td>
          <td><?= $item['quantity'] ?></td>
          <td><?= number_format($item['price']) ?> đ</td>
          <td><?= number_format($item['quantity'] * $item['price']) ?> đ</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <h4 class="text-danger">
    Tổng: <?= number_format($order['total']) ?> đ
  </h4>
</div>

<?php
$content = ob_get_clean();
view('layouts.StoreLayout', [
  'title' => 'Chi tiết đơn',
  'content' => $content
]);
?>