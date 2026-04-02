<?php ob_start(); ?>
<div class="container py-5">
  <div class="row justify-content-center">
    
    <div class="col-lg-8">
      <div class="card shadow-lg border-0 rounded-4">
        
        <!-- Header -->
        <div class="card-header bg-success text-white text-center rounded-top-4">
          <h4 class="mb-0">👤 Thông tin tài khoản</h4>
        </div>

        <div class="card-body p-4">

          <!-- Alert -->
          <?php if (!empty($flashSuccess)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($flashSuccess) ?></div>
          <?php endif; ?>

          <?php if (!empty($flashError)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($flashError) ?></div>
          <?php endif; ?>

          <div class="row">
            
            <!-- Avatar -->
            <div class="col-md-4 text-center mb-3">
              <img src="https://i.pravatar.cc/150" 
                   class="rounded-circle mb-3 shadow"
                   width="120" height="120">
              <p class="text-muted">Tài khoản của bạn</p>
            </div>

            <!-- Form -->
            <div class="col-md-8">
              <form method="post" action="<?= BASE_URL ?>profile/update">

                <div class="mb-3">
                  <label class="form-label fw-semibold">Họ tên</label>
                  <input type="text" 
                         class="form-control rounded-3" 
                         name="fullname"
                         value="<?= htmlspecialchars($profile['fullname'] ?? '') ?>" 
                         required>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-semibold">Số điện thoại</label>
                  <input type="text" 
                         class="form-control rounded-3" 
                         name="phone"
                         value="<?= htmlspecialchars($profile['phone'] ?? '') ?>" 
                         required>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-semibold">Địa chỉ</label>
                  <textarea class="form-control rounded-3" 
                            rows="3" 
                            name="address" 
                            required><?= htmlspecialchars($profile['address'] ?? '') ?></textarea>
                </div>

                <button class="btn btn-success w-100 rounded-3">
                  💾 Lưu thông tin
                </button>

              </form>
            </div>

          </div>
        </div>

      </div>
    </div>

  </div>
</div>

<?php
$content = ob_get_clean();
view('layouts.StoreLayout', [
  'title' => $title ?? 'Thông tin tài khoản',
  'content' => $content
]);
?>