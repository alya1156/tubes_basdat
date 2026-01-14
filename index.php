<?php

include 'config.php';
include 'header.php';

if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?= $_SESSION['type']; ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php 
    unset($_SESSION['message']);
    unset($_SESSION['type']);
endif; 

$query = mysqli_query($koneksi, "SELECT * FROM mahasiswa");

?>

<table class="table table-bordered">
    <tr>
        <th>No</th>
        <th>NPM</th>
        <th>Nama Lengkap</th>
        <th>Kelas</th>
        <th>Email</th>
        <th>Aksi</th>
    </tr>

<?php

$no = 1;
while ($data = mysqli_fetch_array($query)) {
?>

<tr>
    <td><?= $no++ ?></td>
    <td><?= $data['npm']?></td>
    <td><?= $data['nama_lengkap']?></td>
    <td><?= $data['kelas'] ?></td>
    <td><?= $data['email'] ?></td>
    <td>
        <button class="btn btn-warning btn-sm"data-bs-toggle="modal"data-bs-target="#modalEdit<?= $data['id_mahasiswa']; ?>">Edit</button>
        <button class="btn btn-danger btn-sm"data-bs-toggle="modal"data-bs-target="#confirmHapus<?= $data['id_mahasiswa']; ?>">Hapus</button>
    </td>

    <div class="modal fade" id="modalEdit<?= $data['id_mahasiswa']; ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Konfirmasi Edit Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        Apakah Anda yakin ingin mengedit data mahasiswa ini?
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <a href="edit.php?id=<?= $data['id_mahasiswa']; ?>" class="btn btn-warning">
            Ya, Lanjutkan
        </a>
      </div>

    </div>
  </div>
</div>



    <!-- Modal Tombol Hapus Start -->
    <div class="modal fade" id="confirmHapus<?= $data['id_mahasiswa']; ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data mahasiswa ini?
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="hapus.php?id=<?= $data['id_mahasiswa']; ?>" class="btn btn-danger">
                    Ya, Hapus
                </a>
            </div>

            </div>
        </div>
    </div>
    <!-- Modal Tombol Hapus End -->

</tr>

<?php } ?>

</table>

<?php include 'footer.php'; ?>