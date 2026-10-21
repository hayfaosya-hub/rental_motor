
<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

if ($nama == '' || $username == '' || $password == '') {
    header("Location: user_tambah.php?pesan=Semua+kolom+wajib+diisi");
    exit;
}

if (!in_array($role, ['admin', 'petugas', 'penyewa'])) {
    header("Location: user_tambah.php?pesan=Role+tidak+valid");
    exit;
}

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id_user FROM user WHERE username = ?"
);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($hasil) > 0) {
    header("Location: user_tambah.php?pesan=Username+sudah+digunakan");
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = mysqli_prepare(
    $koneksi,
    "INSERT INTO user (nama, username, password, role)
     VALUES (?, ?, ?, ?)"
);
mysqli_stmt_bind_param($stmt, "ssss", $nama, $username, $hash, $role);

if (mysqli_stmt_execute($stmt)) {
    header("Location: user.php?status=tambah");
    exit;
}

header("Location: user_tambah.php?pesan=Gagal+menambahkan+user");
exit;
?>
