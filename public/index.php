<?php

session_start();

require_once "../config/db.php";

// CSRF Token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Pencarian
$search = trim($_GET["search"] ?? "");

if ($search !== "") {

    $stmt = $pdo->prepare(
        "SELECT * FROM products
         WHERE name LIKE ? OR category LIKE ?
         ORDER BY id DESC"
    );

    $keyword = "%" . $search . "%";
    $stmt->execute([$keyword, $keyword]);

} else {

    $stmt = $pdo->query(
        "SELECT * FROM products ORDER BY id DESC"
    );
}

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Product Manager</title>

    <link rel="stylesheet" href="assets/style.css">

</head>

<body>

<div class="container">

    <!-- HEADER -->
    <div class="page-header">

        <div>
            <p class="form-label">PRODUCT MANAGER</p>

            <h1>Daftar Produk</h1>

            <p>Kelola produk dengan mudah dan praktis.</p>
        </div>

        <a href="create.php" class="btn-tambah">
            ＋ Tambah Produk
        </a>

    </div>


    <!-- PESAN BERHASIL -->

    <?php if (isset($_GET["status"]) && $_GET["status"] === "created"): ?>

        <p class="success">
            ✓ Produk berhasil ditambahkan.
        </p>

    <?php elseif (isset($_GET["status"]) && $_GET["status"] === "updated"): ?>

        <p class="success">
            ✓ Produk berhasil diperbarui.
        </p>

    <?php elseif (isset($_GET["status"]) && $_GET["status"] === "deleted"): ?>

        <p class="success">
            ✓ Produk berhasil dihapus.
        </p>

    <?php endif; ?>


    <!-- SEARCH -->

    <form method="GET" class="search-box">

        <input
            type="text"
            name="search"
            placeholder="🔍 Cari nama atau kategori produk..."
            value="<?= htmlspecialchars($search) ?>"
        >

        <button type="submit">
            Cari
        </button>

        <?php if ($search !== ""): ?>

            <a href="index.php" class="btn-reset">
                Reset
            </a>

        <?php endif; ?>

    </form>


    <!-- HASIL PENCARIAN -->

    <?php if ($search !== ""): ?>

        <p class="search-result">
            Hasil pencarian untuk:
            <strong>"<?= htmlspecialchars($search) ?>"</strong>
        </p>

    <?php endif; ?>


    <!-- DAFTAR PRODUK -->

    <?php if (empty($products)): ?>

        <div class="empty-state">

            <div class="empty-icon">📦</div>

            <h2>Produk tidak ditemukan</h2>

            <p>
                Coba gunakan kata kunci lain atau tambahkan produk baru.
            </p>

        </div>

    <?php else: ?>

        <div class="product-list">

            <?php foreach ($products as $product): ?>

                <div class="product-card">

                    <div class="product-info">

                        <span class="product-category">
                            <?= htmlspecialchars($product["category"]) ?>
                        </span>

                        <h2>
                            <?= htmlspecialchars($product["name"]) ?>
                        </h2>

                        <div class="product-details">

                            <div>
                                <span>Harga</span>

                                <strong>
                                    Rp <?= number_format($product["price"], 0, ',', '.') ?>
                                </strong>
                            </div>

                            <div>
                                <span>Stok</span>

                                <strong>
                                    <?= htmlspecialchars($product["stock"]) ?>
                                </strong>
                            </div>

                        </div>

                    </div>


                    <!-- AKSI -->

                    <div class="actions">

                        <a
                            href="edit.php?id=<?= $product["id"] ?>"
                            class="btn-edit"
                        >
                            ✏️ Edit
                        </a>


                        <form
                            method="POST"
                            action="delete.php"
                            onsubmit="return confirm('Yakin ingin menghapus produk ini?');"
                        >

                            <input
                                type="hidden"
                                name="id"
                                value="<?= $product["id"] ?>"
                            >

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= $_SESSION["csrf_token"] ?>"
                            >

                            <button
                                type="submit"
                                class="btn-delete"
                            >
                                🗑️ Hapus
                            </button>

                        </form>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

</body>

</html>