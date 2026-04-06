<?php ob_start(); ?>

<div class="container py-4">
  <h3 class="mb-4">📊 Dashboard</h3>

  <!-- CARDS -->
  <div class="row mb-4">

    <div class="col-md-4">
      <div class="card p-3 shadow-sm">
        <h5>🧾 Đơn hàng</h5>
        <h2><?= $totalOrders ?></h2>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card p-3 shadow-sm">
        <h5>💰 Doanh thu</h5>
        <h2 class="text-danger"><?= number_format($revenue) ?> đ</h2>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card p-3 shadow-sm">
        <h5>📦 Sản phẩm</h5>
        <h2><?= $totalProducts ?></h2>
      </div>
    </div>

  </div>

  <!-- CHART -->
  <div class="card p-3 shadow-sm">
    <h5>📈 Doanh thu theo tháng</h5>
    <canvas id="revenueChart"></canvas>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const labels = <?= json_encode(array_column($chartData, 'month')) ?>;
const data = <?= json_encode(array_column($chartData, 'revenue')) ?>;

new Chart(document.getElementById('revenueChart'), {
  type: 'bar',
  data: {
    labels: labels.map(m => 'Tháng ' + m),
    datasets: [{
      label: 'Doanh thu',
      data: data
    }]
  }
});
</script>

<?php
$content = ob_get_clean();
view('layouts.AdminLayout', [
  'title' => 'Dashboard',
  'content' => $content
]);
?>