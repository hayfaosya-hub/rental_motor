
<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST'
    || !isset($_POST['id_motor'])
    || !ctype_digit($_POST['id_motor'])) {
    header("Location: motor.php");
    exit;
}

$id = (int) $_POST['id_motor'];

$query = mysqli_prepare($koneksi, "DELETE FROM motor WHERE id_motor = ?");
mysqli_stmt_bind_param($query, "i", $id);
mysqli_stmt_execute($query);

header("Location: motor.php");
exit;
?>
