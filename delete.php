<?php
include 'config.php';
$id = $_GET['id'];
$sql = "DELETE FROM mahasiswa WHERE id_mahasiswa = '$id'";
$result = mysqli_query($koneksi, $sql);
if ($result) {
$_SESSION['message'] = "Data mahasiswa berhasil dihapus.";
$_SESSION['type'] = "success";
} else {
$_SESSION['message'] = "Gagal menghapus data mahasiswa.";
$_SESSION['type'] = "danger";
}
header("Location: index.php");
exit;
?>
