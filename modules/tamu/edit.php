<?php
// This file redirects to tambah.php with the ID parameter
// It allows for a cleaner separation of edit from add functionality
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    header('Location: /tubes_basdat/modules/tamu/list.php');
    exit;
}

// Include and process tambah.php with the edit ID
$_GET['id'] = $id;
include 'tambah.php';
