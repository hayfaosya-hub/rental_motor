
<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST'
    || !isset($_POST['id_kategori'])
    || !ctype_digit($_POST['id_kategori'])) {
    header("Location: kategori.php");
    exit;
}

$id = (int) $_POST['id_kategori'];

$query = mysqli_prepare(
    $koneksi,
    "DELETE FROM kategori WHERE id_kategori = ?"
);
mysqli_stmt_bind_param($query, "i", $id);

if (mysqli_stmt_execute($query)) {
    header("Location: kategori.php?pesan=berhasil");
    exit;
} else {
    header("Location: kategori.php?pesan=terpakai");
    exit;
}
?>
