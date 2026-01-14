<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

requireAdminLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM tamu WHERE id_tamu = ?");
    if ($stmt->execute([$id])) {
        redirectWithMessage('/tubes_basdat/modules/tamu/list.php', 'Data tamu berhasil dihapus', 'success');
    } else {
        redirectWithMessage('/tubes_basdat/modules/tamu/list.php', 'Gagal menghapus data tamu', 'danger');
    }
}

header('Location: /tubes_basdat/modules/tamu/list.php');
