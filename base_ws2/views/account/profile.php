<?php ob_start(); ?>
<div class="container py-4">
  <h2 class="mb-3">Thông tin tài khoản</h2>
  <?php if (!empty($flashSuccess)): ?><div class="alert alert-success"><?= htmlspecialchars($flashSuccess) ?></div><?php endif; ?>
  <?php if (!empty($flashError)): ?><div class="alert alert-danger"><?= htmlspecialchars($flashError) ?></div><?php endif; ?>
  <div class="card">
    <div class="card-body">
      <form method="post" action="<?= BASE_URL ?>profile/update">
        <div class="mb-3">
          <label class="form-label">Họ tên</label>
          <input type="text" class="form-control" name="fullname" value="<?= htmlspecialchars($profile['fullname'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Số điện thoại</label>
          <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Địa chỉ</label>
          <textarea class="form-control" rows="3" name="address" required><?= htmlspecialchars($profile['address'] ?? '') ?></textarea>
        </div>
        <button class="btn btn-success">Lưu thông tin</button>
      </form>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
view('layouts.StoreLayout', ['title' => $title ?? 'Thông tin tài khoản', 'content' => $content]);
?>
