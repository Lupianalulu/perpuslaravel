<?php
include 'config.php';

function getAllPeminjaman()
{
    global $koneksi;
    $query = "SELECT * FROM peminjaman_user";
    $result = $koneksi->query($query);

    if (!$result) {
        die("Error: " . $koneksi->error);
    }

    $peminjaman = [];
    while ($row = $result->fetch_assoc()) {
        $peminjaman[] = $row;
    }

    return $peminjaman;
}

function getPeminjamanById($id)
{
    global $koneksi;
    $query = "SELECT * FROM peminjaman_user WHERE id = $id";
    $result = $koneksi->query($query);

    if (!$result) {
        die("Error: " . $koneksi->error);
    }

    return $result->fetch_assoc();
}

function addPeminjaman($user_id, $buku_id, $nama_peminjam, $nim_nis, $judul_buku, $durasi_peminjaman, $tanggal_peminjaman)
{
    global $koneksi;
    $nama_peminjam = $koneksi->real_escape_string($nama_peminjam);
    $nim_nis = $koneksi->real_escape_string($nim_nis);
    $judul_buku = $koneksi->real_escape_string($judul_buku);
    $durasi_peminjaman = (int)$durasi_peminjaman;

    $query = "INSERT INTO peminjaman_user (user_id, buku_id, nama_peminjam, nim_nis, judul_buku, durasi_peminjaman, tanggal_peminjaman)
              VALUES ($user_id, $buku_id, '$nama_peminjam', '$nim_nis', '$judul_buku', $durasi_peminjaman, '$tanggal_peminjaman')";

    $result = $koneksi->query($query);
    if (!$result) {
        die("Error: " . $koneksi->error);
    }
}

function editPeminjaman($id, $nama_peminjam, $nim_nis, $judul_buku, $durasi_peminjaman, $tanggal_pengembalian, $status_pengembalian, $denda)
{
    global $koneksi;
    $nama_peminjam = $koneksi->real_escape_string($nama_peminjam);
    $nim_nis = $koneksi->real_escape_string($nim_nis);
    $judul_buku = $koneksi->real_escape_string($judul_buku);
    $durasi_peminjaman = (int)$durasi_peminjaman;
    $denda = (int)$denda;

    $query = "UPDATE peminjaman_user
              SET nama_peminjam='$nama_peminjam', nim_nis='$nim_nis', judul_buku='$judul_buku',
                  durasi_peminjaman=$durasi_peminjaman, tanggal_pengembalian='$tanggal_pengembalian',
                  status_pengembalian='$status_pengembalian', denda=$denda
              WHERE id=$id";

    $result = $koneksi->query($query);
    if (!$result) {
        die("Error: " . $koneksi->error);
    }
}

function deletePeminjaman($id)
{
    global $koneksi;
    $query = "DELETE FROM peminjaman_user WHERE id = $id";

    $result = $koneksi->query($query);
    if (!$result) {
        die("Error: " . $koneksi->error);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_peminjaman'])) {
        $user_id = $_POST['user_id'];
        $buku_id = $_POST['buku_id'];
        $nama_peminjam = $_POST['nama_peminjam'];
        $nim_nis = $_POST['nim_nis'];
        $judul_buku = $_POST['judul_buku'];
        $durasi_peminjaman = $_POST['durasi_peminjaman'];
        $tanggal_peminjaman = date('Y-m-d');

        addPeminjaman($user_id, $buku_id, $nama_peminjam, $nim_nis, $judul_buku, $durasi_peminjaman, $tanggal_peminjaman);
    } elseif (isset($_POST['edit_peminjaman'])) {
        $id = $_POST['edit_id'];
        $nama_peminjam = $_POST['edit_nama_peminjam'];
        $nim_nis = $_POST['edit_nim_nis'];
        $judul_buku = $_POST['edit_judul_buku'];
        $durasi_peminjaman = $_POST['edit_durasi_peminjaman'];
        $tanggal_pengembalian = $_POST['edit_tanggal_pengembalian'];
        $status_pengembalian = $_POST['edit_status_pengembalian'];
        $denda = $_POST['edit_denda'];

        editPeminjaman($id, $nama_peminjam, $nim_nis, $judul_buku, $durasi_peminjaman, $tanggal_pengembalian, $status_pengembalian, $denda);
    } elseif (isset($_POST['delete_peminjaman'])) {
        $id = $_POST['delete_id'];
        deletePeminjaman($id);
    }
}

$peminjamanList = getAllPeminjaman();
?>

<?php include('inc/head.php') ?>
<body>
<div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <div class="container mt-5">
    <h2>Histori Peminjaman</h2>
    <button type="button" class="btn btn-primary m-2" data-toggle="modal" data-target="#tambahPeminjamanModal">Tambah Peminjaman</button>

    <div class="row">
        <div class="col">
            <div class="table-responsive">
                <table class="table mt-3">
                    <thead>
                    <tr>
                        <th>Nama Peminjam</th>
                        <th>NIM/NIS</th>
                        <th>Judul Buku</th>
                        <th>Durasi Peminjaman</th>
                        <th>Denda</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($peminjamanList as $peminjaman) : ?>
                        <tr>
                            <td><?= $peminjaman['nama_peminjam']; ?></td>
                            <td><?= $peminjaman['nim_nis']; ?></td>
                            <td><?= $peminjaman['judul_buku']; ?></td>
                            <td><?= $peminjaman['durasi_peminjaman']; ?> hari</td>
                            <td><?= $peminjaman['denda']; ?></td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                        data-target="#editPeminjamanModal<?= $peminjaman['id']; ?>">Edit
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                        data-target="#hapusPeminjamanModal<?= $peminjaman['id']; ?>">Hapus
                                </button>
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#detailPeminjamanModal<?= $peminjaman['id']; ?>">
                        Detail
                    </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="detailPeminjamanModal<?= $peminjaman['id']; ?>" tabindex="-1" aria-labelledby="detailPeminjamanModalLabel<?= $peminjaman['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="detailPeminjamanModalLabel<?= $peminjaman['id']; ?>">Detail Peminjaman</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <!-- Isi dengan detail peminjaman -->
                                    <p>Nama Peminjam: <?= $peminjaman['nama_peminjam']; ?></p>
                                    <p>NIM/NIS: <?= $peminjaman['nim_nis']; ?></p>
                                    <p>Judul Buku: <?= $peminjaman['judul_buku']; ?></p>
                                    <p>Durasi Peminjaman:<?= $peminjaman['durasi_peminjaman']; ?> Hari</p>
                                    <p>Tanggal Peminjaman:<?= $peminjaman['tanggal_peminjaman']; ?></p>
                                    <p>Tanggal Pengembalian: <?= $peminjaman['tanggal_pengembalian']; ?></p>
                                    <p>Status: <?= $peminjaman['status_pengembalian']; ?></p>
                                    <p>Denda: <?= $peminjaman['denda']; ?></p>
                                    
                                    <!-- Tambahkan informasi lainnya sesuai kebutuhan -->
                                </div>
                            </div>
                        </div>
                    </div>

                        <!-- Modal Edit Peminjaman -->
                        <div class="modal fade" id="editPeminjamanModal<?= $peminjaman['id']; ?>" tabindex="-1"
                             aria-labelledby="editPeminjamanModalLabel<?= $peminjaman['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editPeminjamanModalLabel<?= $peminjaman['id']; ?>">Edit Peminjaman</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post">
                                            <input type="hidden" name="edit_id" value="<?= $peminjaman['id']; ?>">
                                            <div class="form-group">
                                                <label for="edit_nama_peminjam">Nama Peminjam:</label>
                                                <input type="text" class="form-control" id="edit_nama_peminjam"
                                                       name="edit_nama_peminjam" value="<?= $peminjaman['nama_peminjam']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="edit_nim_nis">NIM/NIS:</label>
                                                <input type="text" class="form-control" id="edit_nim_nis" name="edit_nim_nis"
                                                       value="<?= $peminjaman['nim_nis']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="edit_judul_buku">Judul Buku:</label>
                                                <input type="text" class="form-control" id="edit_judul_buku"
                                                       name="edit_judul_buku" value="<?= $peminjaman['judul_buku']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="edit_durasi_peminjaman">Durasi Peminjaman (hari):</label>
                                                <input type="number" class="form-control" id="edit_durasi_peminjaman"
                                                       name="edit_durasi_peminjaman" value="<?= $peminjaman['durasi_peminjaman']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="edit_tanggal_pengembalian">Tanggal Pengembalian:</label>
                                                <input type="date" class="form-control" id="edit_tanggal_pengembalian"
                                                       name="edit_tanggal_pengembalian" value="<?= $peminjaman['tanggal_pengembalian']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="edit_status_pengembalian">Status Pengembalian:</label>
                                                <select class="form-control" id="edit_status_pengembalian"
                                                        name="edit_status_pengembalian" required>
                                                    <option value="Belum Dikembalikan" <?= ($peminjaman['status_pengembalian'] == 'Belum Dikembalikan') ? 'selected' : ''; ?>>
                                                        Belum Dikembalikan
                                                    </option>
                                                    <option value="Sudah Dikembalikan" <?= ($peminjaman['status_pengembalian'] == 'Sudah Dikembalikan') ? 'selected' : ''; ?>>
                                                        Sudah Dikembalikan
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="edit_denda">Denda:</label>
                                                <input type="number" class="form-control" id="edit_denda" name="edit_denda"
                                                       value="<?= $peminjaman['denda']; ?>" required>
                                            </div>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary" name="edit_peminjaman">Simpan Perubahan</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal Hapus Peminjaman -->
                        <div class="modal fade" id="hapusPeminjamanModal<?= $peminjaman['id']; ?>" tabindex="-1"
                             aria-labelledby="hapusPeminjamanModalLabel<?= $peminjaman['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="hapusPeminjamanModalLabel<?= $peminjaman['id']; ?>">Hapus Peminjaman</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus peminjaman ini?
                                    </div>
                                    <div class="modal-footer">
                                        <form method="post">
                                            <input type="hidden" name="delete_id" value="<?= $peminjaman['id']; ?>">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger" name="delete_peminjaman">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Peminjaman -->
    <div class="modal fade" id="tambahPeminjamanModal" tabindex="-1" aria-labelledby="tambahPeminjamanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahPeminjamanModalLabel">Tambah Peminjaman</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <div class="form-group">
                            <label for="user_id">ID Pengguna:</label>
                            <input type="number" class="form-control" id="user_id" name="user_id" required>
                        </div>
                        <div class="form-group">
                            <label for="buku_id">ID Buku:</label>
                            <input type="number" class="form-control" id="buku_id" name="buku_id" required>
                        </div>
                        <div class="form-group">
                            <label for="nama_peminjam">Nama Peminjam:</label>
                            <input type="text" class="form-control" id="nama_peminjam" name="nama_peminjam" required>
                        </div>
                        <div class="form-group">
                            <label for="nim_nis">NIM/NIS:</label>
                            <input type="text" class="form-control" id="nim_nis" name="nim_nis" required>
                        </div>
                        <div class="form-group">
                            <label for="judul_buku">Judul Buku:</label>
                            <input type="text" class="form-control" id="judul_buku" name="judul_buku" required>
                        </div>
                        <div class="form-group">
                            <label for="durasi_peminjaman">Durasi Peminjaman (hari):</label>
                            <input type="number" class="form-control" id="durasi_peminjaman" name="durasi_peminjaman" required>
                        </div>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" name="add_peminjaman">Tambah Peminjaman</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
              </div>
            </div>
          </div>
        </div>
      </div>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<?php include('inc/foot.php');?>