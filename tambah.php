<?php
include 'config.php';

/* PROSES SIMPAN — HARUS PALING ATAS */
if (isset($_POST['simpan'])) {
    $npm   = $_POST['npm'];
    $nama  = $_POST['nama_lengkap'];
    $kelas = $_POST['kelas'];
    $email = $_POST['email'];

    $sql = "INSERT INTO mahasiswa (npm, nama_lengkap, kelas, email)
            VALUES ('$npm', '$nama', '$kelas', '$email')";

    mysqli_query($koneksi, $sql) or die(mysqli_error($koneksi));

    $_SESSION['message'] = "Data mahasiswa berhasil ditambahkan.";
    $_SESSION['type'] = "success";

    header("Location: index.php");
    exit;
}
?>

<?php include 'header.php'; ?>

<form method="post">
  <div class="mb-3">
    <label>NPM</label>
    <input type="number" name="npm" class="form-control" required>
  </div>

  <div class="mb-3">
    <label>Nama Mahasiswa</label>
    <input type="text" name="nama_lengkap" class="form-control" required>
  </div>

  <div class="mb-3">
    <label>Kelas</label>
    <input type="text" name="kelas" class="form-control" required>
  </div>

  <div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control" required>
  </div>

  <div class="d-flex justify-content-end gap-2 mt-4">
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmTambah">
      Simpan
    </button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
  </div>

  <!-- MODAL -->
  <div class="modal fade" id="confirmTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Konfirmasi Penyimpanan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          Apakah data mahasiswa yang Anda input sudah sesuai?
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Batal
          </button>
          <button type="submit" name="simpan" class="btn btn-success">
            Ya, Simpan
          </button>
        </div>

      </div>
    </div>
  </div>
</form>

<?php include 'footer.php'; ?>
