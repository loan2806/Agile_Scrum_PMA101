<h2>Chi tiết đơn hàng #<?= $order['id'] ?></h2>

<p><strong>Khách hàng:</strong> <?= $order['customer_name'] ?></p>
<p><strong>Tổng tiền:</strong> <?= number_format($order['total']) ?> VND</p>
<p><strong>Trạng thái:</strong> <?= $order['status'] ?></p>

<h4>Sản phẩm:</h4>
<table class="table">
    <tr>
        <th>Tên</th>
        <th>Số lượng</th>
        <th>Giá</th>
    </tr>
    <?php foreach ($orderItems as $item): ?>
        <tr>
            <td><?= $item['product_name'] ?></td>
            <td><?= $item['quantity'] ?></td>
            <td><?= number_format($item['price']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<form method="POST" action="<?= BASE_URL ?>admin/orders/update-status">
    <input type="hidden" name="id" value="<?= $order['id'] ?>">

    <select name="status" class="form-control">
        <option value="pending">Pending</option>
        <option value="processing">Processing</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
    </select>

    <button class="btn btn-primary mt-2">Cập nhật trạng thái</button>
</form>