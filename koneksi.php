
<?php
$koneksi = mysqli_connect(
    "localhost",
    "root",
    "",
    "rental_motor"
);

if (!$koneksi) {
    die("Koneksi database gagal");
}
?>
