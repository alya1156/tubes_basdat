<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pengelolaan Data Mahasiswa</title>

<!-- Bootstrap CSS (FIXED) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="style.css">
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="index.php">Data Mahasiswa</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'index.php') ? 'active fw-semibold' : 'text-white-50'; ?>" href="index.php">
            Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'tambah.php') ? 'active fw-semibold' : 'text-white-50'; ?>" href="tambah.php">
            Tambah Data
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
<div class="content-wrapper">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
rel="stylesheet">
