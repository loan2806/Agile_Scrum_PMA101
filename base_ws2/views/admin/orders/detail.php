<?php ob_start(); ?>

<div class="container-fluid py-3 px-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">📦 Chi tiết đơn hàng #<?= $order['order_id'] ?></h5>

        <a href="<?= BASE_URL ?>?act=admin/orders" class="btn btn-sm btn-secondary">
            ← Quay lại
        </a>
    </div>

    <div class="row g-3">

        <!-- Thông tin đơn hàng -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h6 class="fw-bold mb-3">Thông tin khách hàng</h6>

                    <p class="mb-1"><strong>👤 Tên:</strong> <?= $order['fullname'] ?></p>
                    <p class="mb-1"><strong>📞 SĐT:</strong> <?= $order['phone'] ?></p>
                    <p class="mb-2"><strong>📍 Địa chỉ:</strong> <?= $order['address'] ?></p>

                    <hr>

                    <h6 class="fw-bold mb-2">Thông tin đơn hàng</h6>

                    <p class="mb-1"><strong>📅 Ngày:</strong> <?= date('d/m/Y', strtotime($order['order_date'])) ?></p>

                    <?php
                    $statusClass = [
                        'Đang xử lý' => 'secondary',
                        'Đang giao' => 'warning text-dark',
                        'Đã giao' => 'success',
                        'Đã hủy' => 'danger'
                    ];
                    ?>

                    <p class="mb-2">
                        <strong>Trạng thái:</strong>
                        <span class="badge bg-<?= $statusClass[$order['status']] ?? 'secondary' ?>">
                            <?= $order['status'] ?>
                        </span>
                    </p>

                    <p class="fw-bold text-danger">
                        💰 Tổng tiền: <?= number_format($order['total']) ?> đ
                    </p>

                    <!-- Update trạng thái -->
                    <form method="post" action="<?= BASE_URL ?>?act=admin/orders/update">
                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">

                        <select name="status" class="form-select form-select-sm mb-2">
                            <option <?= $order['status'] == 'Đang xử lý' ? 'selected' : '' ?>>Đang xử lý</option>
                            <option <?= $order['status'] == 'Đang giao' ? 'selected' : '' ?>>Đang giao</option>
                            <option <?= $order['status'] == 'Đã giao' ? 'selected' : '' ?>>Đã giao</option>
                            <option <?= $order['status'] == 'Đã hủy' ? 'selected' : '' ?>>Đã hủy</option>
                        </select>

                        <button class="btn btn-sm btn-success w-100">✔ Cập nhật trạng thái</button>
                    </form>

                </div>
            </div>
        </div>

        <!-- Danh sách sản phẩm -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h6 class="fw-bold mb-3">🛒 Sản phẩm trong đơn</h6>

                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tên sản phẩm</th>
                                <th width="100">Số lượng</th>
                                <th width="150">Giá</th>
                                <th width="150">Thành tiền</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= $item['name'] ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td><?= number_format($item['price']) ?> đ</td>
                                    <td class="fw-bold text-danger">
                                        <?= number_format($item['price'] * $item['quantity']) ?> đ
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>

                </div>
            </div>
        </div>

    </div>

</div>

<style>
    body {
        background: #f5f5f5;
    }
</style>

<?php
$content = ob_get_clean();
view('layouts.AdminLayout', [
    'title' => 'Chi tiết đơn hàng',
    'content' => $content
]);
?>