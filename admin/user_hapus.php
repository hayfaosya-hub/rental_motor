
<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: user.php");
    exit;
}

$id = (int) ($_POST['id_user'] ?? 0);

if ($id <= 0 || $id == (int) $_SESSION['id_user']) {
    header("Location: user.php");
    exit;
}

$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM user WHERE id_user = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
    header("Location: user.php?status=hapus");
} else {
    header("Location: user.php");
}
exit;
?>
