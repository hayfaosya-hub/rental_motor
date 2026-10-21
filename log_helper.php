
<?php
function catatAktivitas($koneksi, $id_user, $aktivitas)
{
    $sql = "INSERT INTO log_aktivitas (id_user, aktivitas)
            VALUES (?, ?)";

    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt,
            "is",
            $id_user,
            $aktivitas
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}
?>
