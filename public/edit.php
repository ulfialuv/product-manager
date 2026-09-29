<?php

require_once "../config/db.php";

$id = $_GET["id"] ?? null;

if (!$id) {
    die("ID produk tidak ditemukan.");
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Produk tidak ditemukan.");
}

$errors = [];

$name = $product["name"];
$category = $product["category"];
$price = $product["price"];
$stock = $product["stock"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $price = filter_input(INPUT_POST, "price", FILTER_VALIDATE_FLOAT);
    $stock = filter_input(INPUT_POST, "stock", FILTER_VALIDATE_INT);

    if (mb_strlen($name) < 3) {
        $errors["name"] = "Nama minimal 3 karakter.";
    }

    if ($price === false || $price <= 0) {
        $errors["price"] = "Harga harus lebih dari 0.";
    }

    if ($stock === false || $stock < 0) {
        $errors["stock"] = "Stok tidak boleh negatif.";
    }

    if (empty($errors)) {

        $stmt = $pdo->prepare(
            "SELECT id FROM products WHERE name = ? AND id != ?"
        );

        $stmt->execute([$name, $id]);

        if ($stmt->fetch()) {
            $errors["name"] = "Nama produk sudah digunakan.";
        }
    }

    if (empty($errors)) {

        $stmt = $pdo->prepare(
            "UPDATE products
             SET name = ?, category = ?, price = ?, stock = ?
             WHERE id = ?"
        );

        $stmt->execute([
            $name,
            $category,
            $price,
            $stock,
            $id
        ]);

        header("Location: index.php?status=updated");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Produk</title>

    <link rel="stylesheet" href="assets/style.css">

</head>

<body>

<div class="form-container">

    <div class="form-card">

        <div class="form-header">
            <h1>Edit Produk</h1>
            <p>Ubah informasi produk yang dipilih.</p>
        </div>

        <form method="POST">

            <div class="form-group">

                <label>Nama Produk</label>

                <input
                    type="text"
                    name="name"
                    value="<?= htmlspecialchars($name) ?>"
                    minlength="3"
                    required
                >

                <?php if (isset($errors["name"])): ?>
                    <small class="error">
                        <?= htmlspecialchars($errors["name"]) ?>
                    </small>
                <?php endif; ?>

            </div>


            <div class="form-group">

                <label>Kategori</label>

                <input
                    type="text"
                    name="category"
                    value="<?= htmlspecialchars($category) ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>Harga</label>

                <input
                    type="number"
                    name="price"
                    value="<?= htmlspecialchars((string)$price) ?>"
                    min="1"
                    required
                >

                <?php if (isset($errors["price"])): ?>
                    <small class="error">
                        <?= htmlspecialchars($errors["price"]) ?>
                    </small>
                <?php endif; ?>

            </div>


            <div class="form-group">

                <label>Stok</label>

                <input
                    type="number"
                    name="stock"
                    value="<?= htmlspecialchars((string)$stock) ?>"
                    min="0"
                    required
                >

                <?php if (isset($errors["stock"])): ?>
                    <small class="error">
                        <?= htmlspecialchars($errors["stock"]) ?>
                    </small>
                <?php endif; ?>

            </div>


            <div class="form-actions">

                <a href="index.php" class="btn-back">
                    Batal
                </a>

                <button type="submit" class="btn-save">
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>

</body>

</html>