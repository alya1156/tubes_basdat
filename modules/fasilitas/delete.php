<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdminLogin();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) {
    $pdo->prepare("DELETE FROM fasilitas WHERE id_fasilitas = ?")->execute([$id]);
    redirectWithMessage('/tubes_basdat/modules/fasilitas/list.php', 'Fasilitas berhasil dihapus', 'success');
}
header('Location: /tubes_basdat/modules/fasilitas/list.php');
