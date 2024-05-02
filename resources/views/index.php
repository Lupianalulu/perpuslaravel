<?php
include 'admin/config.php';

function getAllBooks()
{
    global $koneksi;
    $query = "SELECT * FROM buku";
    $result = $koneksi->query($query);

    if (!$result) {
        die("Error: " . $koneksi->error);
    }

    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }

    return $books;
}

$books = getAllBooks();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body{
            background: #F6F6F6;
        }
        
        .card{
            background: #F9F7F7;

        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2>Daftar Buku</h2>
    <div class="alert alert-warning" role="alert">
  Anda Belum <a href="home" class="btn btn-primary">Masuk</a> 
</div>
    <div class="row">
        <?php foreach ($books as $book) : ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <?php if (!empty($book['cover_path'])) : ?>
                        <img src="admin/<?= $book['cover_path']; ?>" class="card-img-top" alt="Cover Buku">
                    <?php else : ?>
                        <img src="placeholder_image.jpg" class="card-img-top" alt="Placeholder Image">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= $book['judul']; ?></h5>
                        <p class="card-text">Kode Buku: <?= $book['kode_buku']; ?></p>
                        <p class="card-text">Kategori: <?= $book['kategori']; ?></p>
                        <p class="card-text">Jumlah: <?= $book['jumlah']; ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>

</body>
</html>
