<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

requireAdminLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM tipe_kamar WHERE id_tipe = ?");
    if ($stmt->execute([$id])) {
        redirectWithMessage('/tubes_basdat/modules/tipe_kamar/list.php', 'Tipe kamar berhasil dihapus', 'success');
    }
}

header('Location: /tubes_basdat/modules/tipe_kamar/list.php');
