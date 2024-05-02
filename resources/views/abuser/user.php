<?php
// Sambungkan ke database
include 'config.php';

// Fungsi untuk mendapatkan semua buku yang tersedia
function getAvailableBooks()
{
    global $koneksi;
    $query = "SELECT * FROM buku WHERE jumlah > 0";
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

// Fungsi untuk mendapatkan riwayat peminjaman user
function getLoanHistory($userId)
{
    global $koneksi;
    $query = "SELECT * FROM peminjaman_user WHERE user_id = $userId";
    $result = $koneksi->query($query);

    if (!$result) {
        die("Error: " . $koneksi->error);
    }

    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }

    return $history;
}

// Fungsi untuk mendapatkan nama user yang login
function getUserName($userId)
{
    global $koneksi;
    $query = "SELECT name FROM pengguna WHERE id = $userId";
    $result = $koneksi->query($query);

    if (!$result) {
        die("Error: " . $koneksi->error);
    }

    $user = $result->fetch_assoc();
    return $user['name'];
}

// Fungsi untuk melakukan peminjaman buku
function borrowBook($userId, $bookId, $nimNis, $durasiPeminjaman)
{
    global $koneksi;
    $bookId = (int)$bookId;
    $durasiPeminjaman = (int)$durasiPeminjaman;

    // Pemeriksaan jumlah peminjaman
    $queryCheckLimit = "SELECT jumlah_peminjaman FROM pengguna WHERE id = $userId";
    $resultCheckLimit = $koneksi->query($queryCheckLimit);

    if (!$resultCheckLimit) {
        die("Error: " . $koneksi->error);
    }

    $row = $resultCheckLimit->fetch_assoc();
    $jumlahPeminjaman = $row['jumlah_peminjaman'];

    if ($jumlahPeminjaman >= 3) {
        die("Maaf, Anda telah mencapai batas peminjaman. Tidak dapat meminjam lebih banyak.");
        header("Location: user.php");
        exit();
    }

    // Ambil tanggal peminjaman
    $queryTanggalPeminjaman = "SELECT NOW() AS tanggal_peminjaman";
    $resultTanggalPeminjaman = $koneksi->query($queryTanggalPeminjaman);

    if (!$resultTanggalPeminjaman) {
        die("Error: " . $koneksi->error);
    }

    $rowTanggalPeminjaman = $resultTanggalPeminjaman->fetch_assoc();
    $tanggalPeminjaman = $rowTanggalPeminjaman['tanggal_peminjaman'];

    // Hitung tanggal pengembalian
    $tanggalPengembalian = date('Y-m-d', strtotime($tanggalPeminjaman . ' + ' . $durasiPeminjaman . ' days'));

    // Query untuk meminjam buku
    $query = "INSERT INTO peminjaman_user (user_id, buku_id, nama_peminjam, nim_nis, judul_buku, durasi_peminjaman, tanggal_peminjaman, tanggal_pengembalian, status_pengembalian)
              SELECT $userId, $bookId, '" . getUserName($userId) . "', '$nimNis', judul, $durasiPeminjaman, '$tanggalPeminjaman', '$tanggalPengembalian', 'Belum Dikembalikan' 
              FROM buku WHERE id = $bookId";

    $result = $koneksi->query($query);
    if (!$result) {
        die("Error: " . $koneksi->error);
    }

    // Kurangi jumlah buku yang tersedia
    $queryUpdateBook = "UPDATE buku SET jumlah = jumlah - 1 WHERE id = $bookId";
    $koneksi->query($queryUpdateBook);

    // Update jumlah peminjaman pengguna
    $queryUpdateLimit = "UPDATE pengguna SET jumlah_peminjaman = jumlah_peminjaman + 1 WHERE id = $userId";
    $koneksi->query($queryUpdateLimit);
}

// Fungsi untuk mengembalikan buku
function returnBook($peminjamanId)
{
    global $koneksi;
    $peminjamanId = (int)$peminjamanId;

    // Query untuk mengupdate status_pengembalian menjadi 'Sudah Dikembalikan' dan mendapatkan informasi peminjaman
    $query = "UPDATE peminjaman_user 
              SET status_pengembalian = 'Sudah Dikembalikan', tanggal_pengembalian = NOW() 
              WHERE id = $peminjamanId";

    $result = $koneksi->query($query);
    if (!$result) {
        die("Error: " . $koneksi->error);
    }

    // Tambahkan jumlah buku yang tersedia kembali
    $queryUpdateBook = "UPDATE buku 
                        SET jumlah = jumlah + 1 
                        WHERE id = (SELECT buku_id FROM peminjaman_user WHERE id = $peminjamanId)";
    $koneksi->query($queryUpdateBook);

    // Hapus riwayat peminjaman yang sudah dikembalikan dari database
    $queryDeleteHistory = "DELETE FROM peminjaman_user WHERE id = $peminjamanId";
    $koneksi->query($queryDeleteHistory);
}

// Fungsi untuk mereset riwayat peminjaman pengguna
function resetLoanHistory($userId)
{
    global $koneksi;

    // Ambil data peminjaman yang akan dihapus
    $querySelectHistory = "SELECT buku_id, durasi_peminjaman FROM peminjaman_user WHERE user_id = $userId";
    $resultSelectHistory = $koneksi->query($querySelectHistory);

    if (!$resultSelectHistory) {
        die("Error: " . $koneksi->error);
    }

    // Hapus semua data peminjaman yang dimiliki oleh pengguna
    $queryDeleteHistory = "DELETE FROM peminjaman_user WHERE user_id = $userId";
    $resultDeleteHistory = $koneksi->query($queryDeleteHistory);

    if (!$resultDeleteHistory) {
        die("Error: " . $koneksi->error);
    }

    // Reset jumlah peminjaman pada tabel pengguna menjadi 0
    $queryResetLimit = "UPDATE pengguna SET jumlah_peminjaman = 0 WHERE id = $userId";
    $resultResetLimit = $koneksi->query($queryResetLimit);

    if (!$resultResetLimit) {
        die("Error: " . $koneksi->error);
    }

    // Kembalikan jumlah buku yang dipinjam ke tabel buku
    while ($row = $resultSelectHistory->fetch_assoc()) {
        $bukuId = $row['buku_id'];
        $durasiPeminjaman = $row['durasi_peminjaman'];

        $queryUpdateBook = "UPDATE buku SET jumlah = jumlah + 1 WHERE id = $bukuId";
        $koneksi->query($queryUpdateBook);
    }
}

// Proses pengembalian buku jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kembalikan_buku'])) {
    $peminjamanId = $_POST['peminjaman_id'];
    returnBook($peminjamanId);
}

// Ambil data user yang sedang login
// Misalnya, jika Anda menggunakan session
session_start();
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    // Redirect atau aturan lain jika user tidak login
    header("Location: ../index.php");
    exit();
}

// Proses peminjaman buku jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pinjam_buku'])) {
    $bookId = $_POST['buku_id'];
    $nimNis = $_POST['nim_nis'];
    $durasiPeminjaman = $_POST['durasi_peminjaman'];

    borrowBook($userId, $bookId, $nimNis, $durasiPeminjaman);
}

// Ambil data buku yang tersedia
$availableBooks = getAvailableBooks();

// Ambil riwayat peminjaman user
$loanHistory = getLoanHistory($userId);

// Proses reset riwayat peminjaman jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_history'])) {
    resetLoanHistory($userId);
}

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
    

    

<div class="container mt-5">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="myTabs">
        <li class="nav-item">
            <a class="nav-link active" id="dataBukuTab" data-toggle="tab" href="#dataBuku">Data Buku</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="riwayatTab" data-toggle="tab" href="#riwayat">Riwayat</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="logoutTab" href="logout.php">Logout</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane container active" id="dataBuku">
        <h2>Daftar Buku Tersedia</h2>
    <a href="logout.php">keluar</a>
    <div class="row">
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Cari buku...">
        <?php foreach ($availableBooks as $book) : ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                <span class="btn badge"><?= $book['kode_buku']; ?></span>
                    <img src="../admin/<?= $book['cover_path']; ?>" class="card-img-top" alt="Cover Buku Tidak Tersedia">
                    <div class="card-body">
                        <h5 class="card-title"><?= $book['judul']; ?><br> </h5>
                        <p class="card-text">Kategori: <?= $book['kategori']; ?></p>
                        <p class="card-text">Jumlah Tersedia: <?= $book['jumlah']; ?></p> <br>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#pinjamModal<?= $book['id']; ?>">
                            Pinjam Buku 
                        </button>
                    </div>
                    
                </div>
            </div>

            <!-- Modal Peminjaman -->
            <div class="modal fade" id="pinjamModal<?= $book['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="pinjamModalLabel<?= $book['id']; ?>" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="pinjamModalLabel<?= $book['id']; ?>">Form Peminjaman Buku</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Proses form peminjaman di sini -->
                            <form method="post">
                                <input type="hidden" name="buku_id" value="<?= $book['id']; ?>">
                                <div class="form-group">
                                    <label for="nama_peminjam">Nama Peminjam:</label>
                                    <input type="text" class="form-control" id="nama_peminjam" name="nama_peminjam" value="<?= getUserName($userId); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="nim_nis">NIM/NIS:</label>
                                    <input type="text" class="form-control" id="nim_nis" name="nim_nis" required>
                                </div>
                                <div class="form-group">
                                    <label for="judul_buku">Judul Buku:</label>
                                    <input type="text" class="form-control" id="judul_buku" name="judul_buku" value="<?= $book['judul']; ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="durasi_peminjaman">Durasi Peminjaman (hari):</label>
                                    <input type="number" class="form-control" id="durasi_peminjaman" name="durasi_peminjaman" required max="3">
                                </div>
                                <button type="submit" name="pinjam_buku" class="btn btn-primary">Pinjam</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
        </div>
        <div class="tab-pane container fade" id="riwayat">
            <!-- Isi konten Riwayat di sini -->
            <h2>Riwayat Peminjaman</h2>
    <table class="table mt-3">
        <thead>
            <!-- Tambahkan tombol reset -->
            <form method="post">
                <button type="submit" name="reset_history" class="btn btn-warning mt-3">Reset Riwayat Peminjaman</button>
            </form>

            <tr>
                <th>Judul Buku</th>
                <th>Durasi Peminjaman</th>
                <th>Tanggal Peminjaman</th>
                <th>Tanggal Pengembalian</th>
                <th>Status</th>
                <th>Denda</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($loanHistory as $history) : ?>
                <tr>
                    <td><?= $history['judul_buku']; ?></td>
                    <td><?= $history['durasi_peminjaman']; ?> hari</td>
                    <td><?= $history['tanggal_peminjaman']; ?></td>
                    <td><?= $history['tanggal_pengembalian']; ?></td>
                    <td><?= $history['status_pengembalian']; ?></td>
                    <td><?= $history['denda']; ?></td>
                    <td>
                        <?php if ($history['status_pengembalian'] == 'Belum Dikembalikan') : ?>
                            <form method="post">
                                <input type="hidden" name="peminjaman_id" value="<?= $history['id']; ?>">
                                <button type="submit" name="kembalikan_buku" class="btn btn-danger">Kembalikan</button>
                            </form>
                        <?php else : ?>
                            <span class="badge badge-success">Sudah Dikembalikan</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
<script>
    // Tangkap input pencarian saat nilainya berubah
    $(document).ready(function () {
        $("#searchInput").on("input", function () {
            // Dapatkan nilai pencarian
            var searchValue = $(this).val().toLowerCase();

            // Saring buku yang sesuai dengan pencarian
            $(".card").each(function () {
                var title = $(this).find(".card-title").text().toLowerCase();
                var category = $(this).find(".card-text").eq(0).text().toLowerCase();
                var code = $(this).find(".badge").text().toLowerCase();

                // Tampilkan atau sembunyikan buku berdasarkan pencarian
                if (title.includes(searchValue) || category.includes(searchValue) || code.includes(searchValue)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
</script>

<script>
    // Simpan tab aktif dalam localStorage
    $(document).ready(function () {
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("id");
            localStorage.setItem("activeTab", target);
        });

        // Atur tab yang sedang aktif saat halaman dimuat
        var activeTab = localStorage.getItem("activeTab");
        if (activeTab) {
            $('#myTabs a#' + activeTab).tab('show');
        }
    });
</script>
</body>
</html>
