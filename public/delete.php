<?php

session_start();
require_once "../config/db.php";

// Buat token CSRF
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Pastikan request menggunakan POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Metode request tidak valid.");
}

$id = $_POST["id"] ?? null;
$csrf_token = $_POST["csrf_token"] ?? "";

if (!$id) {
    die("ID produk tidak ditemukan.");
}

// Cek CSRF
if (!hash_equals($_SESSION["csrf_token"], $csrf_token)) {
    die("CSRF token tidak valid.");
}

// Hapus produk
$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php?status=deleted");
exit;