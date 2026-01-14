<?php
include 'config.php';
include 'header.php';
$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE
id_mahasiswa='$id'");
$data = mysqli_fetch_array($query);

if (isset($_POST['update'])) {
$npm = $_POST['npm'];
$nama = $_POST['nama_lengkap'];
$kelas = $_POST['kelas'];
$email = $_POST['email'];
$sql = "UPDATE mahasiswa SET npm='$npm', nama_lengkap='$nama',
kelas='$kelas', email='$email' WHERE id_mahasiswa='$id'";
$result = mysqli_query($koneksi, $sql);
if ($result) {
    if (mysqli_affected_rows($koneksi) > 0) {
$_SESSION['message'] = "Data mahasiswa berhasil diperbarui.";
$_SESSION['type'] = "success";
} else {
$_SESSION['message'] = "Tidak ada perubahan data yang
disimpan.";
$_SESSION['type'] = "warning";
}
} else {
$_SESSION['message'] = "Gagal memperbarui data mahasiswa.";
$_SESSION['type'] = "danger";
}
header("Location: index.php");
exit;
}
?>

<form method="post">
<div class="mb-3">
<label>NPM</label>
<input type="number" name="npm" class="form-control" value="<?=
$data['npm'] ?>" required>
</div>

<div class="mb-3">
<label>Nama Mahasiswa</label>
<input type="text" name="nama_lengkap" class="form-control"
value="<?= $data['nama_lengkap'] ?>" required>
</div>

<div class="mb-3">
<label>Kelas</label>
<input type="text" name="kelas" class="form-control" value="<?=
$data['kelas'] ?>" required>
</div>
<div class="mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control" value="<?=
$data['email'] ?>" required>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">

<button type="button" class="btn btn-warning" data-bs-
toggle="modal" data-bs-target="#confirmEdit">Simpan Perubahan</button>

<a href="index.php" class="btn btn-secondary">Batal</a>
</div>

<div class="modal fade" id="confirmEdit" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Konfirmasi Perubahan</h5>

<button type="button" class="btn-close" data-bs-
dismiss="modal"></button>

</div>

<div class="modal-body">
Apakah perubahan data mahasiswa sudah sesuai?
</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary"

data-bs-dismiss="modal">Batal</button>

<button type="submit" name="update" class="btn btn-
warning">Ya, Perbarui</button>

</div>

</div>
</div>
</div>

</form>

<?php include 'footer.php'; ?>