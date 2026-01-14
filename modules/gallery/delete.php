<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdminLogin();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) {
    $stmt = $pdo->prepare("SELECT foto_path FROM galeri_hotel WHERE id_galeri = ?");
    $stmt->execute([$id]);
    $galeri = $stmt->fetch();
    if ($galeri && file_exists(UPLOAD_PATH . 'gallery/' . $galeri['foto_path'])) {
        unlink(UPLOAD_PATH . 'gallery/' . $galeri['foto_path']);
    }
    $pdo->prepare("DELETE FROM galeri_hotel WHERE id_galeri = ?")->execute([$id]);
    redirectWithMessage('/tubes_basdat/modules/gallery/list.php', 'Foto berhasil dihapus', 'success');
}
header('Location: /tubes_basdat/modules/gallery/list.php');
