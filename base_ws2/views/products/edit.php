<h2>Sửa sản phẩm</h2>

<form method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?>admin/products/update">

    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">

    <div class="mb-3">
        <label>Tên sản phẩm</label>
        <input type="text" name="name" value="<?= $product['name'] ?>" class="form-control">
    </div>

    <div class="mb-3">
        <label>Giá</label>
        <input type="number" name="price" value="<?= $product['price'] ?>" class="form-control">
    </div>

    <div class="mb-3">
        <label>Ảnh</label>
        <input type="file" name="image" class="form-control">
        <br>
        <img src="<?= BASE_URL ?>public/dist/assets/img/<?= $product['image'] ?>" width="100">
    </div>

    <button class="btn btn-primary">Cập nhật</button>
</form>