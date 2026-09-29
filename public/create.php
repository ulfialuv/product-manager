<?php

require_once "../config/db.php";

$errors = [];

$name = "";
$category = "";
$price = "";
$stock = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $price = filter_input(INPUT_POST, "price", FILTER_VALIDATE_FLOAT);
    $stock = filter_input(INPUT_POST, "stock", FILTER_VALIDATE_INT);

    // Validasi nama
    if (mb_strlen($name) < 3) {
        $errors["name"] = "Nama minimal 3 karakter.";
    }

    // Validasi harga
    if ($price === false || $price <= 0) {
        $errors["price"] = "Harga harus lebih dari 0.";
    }

    // Validasi stok
    if ($stock === false || $stock < 0) {
        $errors["stock"] = "Stok tidak boleh negatif.";
    }

    // Cek nama produk sudah ada atau belum
    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "SELECT id FROM products WHERE name = ?"
        );

        $stmt->execute([$name]);

        if ($stmt->fetch()) {
            $errors["name"] = "Nama produk sudah digunakan.";
        }
    }

    // Simpan jika tidak ada error
    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "INSERT INTO products (name, category, price, stock)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->execute([
            $name,
            $category,
            $price,
            $stock
        ]);

        // PRG
        header("Location: index.php?status=created");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Produk - Product Manager</title>

    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

<div class="form-container">

    <div class="form-card">

        <div class="form-header">
            <div>
                <p class="form-label">PRODUCT MANAGER</p>
                <h1>Tambah Produk</h1>
                <p>Tambahkan produk baru ke dalam daftar produk.</p>
            </div>
        </div>

        <form method="POST">

            <!-- Nama Produk -->
            <div class="form-group">

                <label for="name">Nama Produk</label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?= htmlspecialchars($name) ?>"
                    minlength="3"
                    placeholder="Contoh: Kemeja"
                    required
                >

                <?php if (isset($errors["name"])): ?>
                    <p class="error">
                        <?= htmlspecialchars($errors["name"]) ?>
                    </p>
                <?php endif; ?>

            </div>


            <!-- Kategori -->
            <div class="form-group">

                <label for="category">Kategori</label>

                <input
                    type="text"
                    id="category"
                    name="category"
                    value="<?= htmlspecialchars($category) ?>"
                    placeholder="Contoh: Fashion"
                    required
                >

            </div>


            <!-- Harga -->
            <div class="form-group">

                <label for="price">Harga</label>

                <input
                    type="number"
                    id="price"
                    name="price"
                    value="<?= htmlspecialchars((string)$price) ?>"
                    min="1"
                    placeholder="Contoh: 75000"
                    required
                >

                <?php if (isset($errors["price"])): ?>
                    <p class="error">
                        <?= htmlspecialchars($errors["price"]) ?>
                    </p>
                <?php endif; ?>

            </div>


            <!-- Stok -->
            <div class="form-group">

                <label for="stock">Stok</label>

                <input
                    type="number"
                    id="stock"
                    name="stock"
                    value="<?= htmlspecialchars((string)$stock) ?>"
                    min="0"
                    placeholder="Contoh: 20"
                    required
                >

                <?php if (isset($errors["stock"])): ?>
                    <p class="error">
                        <?= htmlspecialchars($errors["stock"]) ?>
                    </p>
                <?php endif; ?>

            </div>


            <!-- Tombol -->
            <div class="form-actions">

                <a href="index.php" class="btn-back">
                    ← Kembali
                </a>

                <button type="submit" class="btn-save">
                    ＋ Simpan Produk
                </button>

            </div>

        </form>

    </div>

</div>

</body>
</html>