<?php
include 'config.php';

// Fungsi untuk mendapatkan semua kategori
function getAllCategories()
{
    global $koneksi;
    $query = "SELECT * FROM kategori";
    $result = $koneksi->query($query);

    if (!$result) {
        die("Error: " . $koneksi->error);
    }

    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    return $categories;
}

// Fungsi untuk menambah kategori baru
function addCategory($nama_kategori)
{
    global $koneksi;
    $nama_kategori = $koneksi->real_escape_string($nama_kategori);

    $query = "INSERT INTO kategori (nama_kategori) VALUES ('$nama_kategori')";

    $result = $koneksi->query($query);
    if (!$result) {
        die("Error: " . $koneksi->error);
    }
}

// Fungsi untuk mengedit kategori
function editCategory($id, $nama_kategori)
{
    global $koneksi;
    $nama_kategori = $koneksi->real_escape_string($nama_kategori);

    $query = "UPDATE kategori SET nama_kategori='$nama_kategori' WHERE id=$id";

    $result = $koneksi->query($query);
    if (!$result) {
        die("Error: " . $koneksi->error);
    }
}

// Fungsi untuk menghapus kategori
function deleteCategory($id)
{
    global $koneksi;
    $query = "DELETE FROM kategori WHERE id = $id";

    $result = $koneksi->query($query);
    if (!$result) {
        die("Error: " . $koneksi->error);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $nama_kategori = $_POST['nama_kategori'];
        addCategory($nama_kategori);
    } elseif (isset($_POST['edit_category'])) {
        $id = $_POST['edit_id'];
        $nama_kategori = $_POST['edit_nama_kategori'];
        editCategory($id, $nama_kategori);
    } elseif (isset($_POST['delete_category'])) {
        $id = $_POST['delete_id'];
        deleteCategory($id);
    }
}

$categories = getAllCategories();
?>

<?php include('inc/head.php') ?>
<body>

<div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Data Kategori</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
    <button type="button" class="btn btn-primary m-2" data-toggle="modal" data-target="#tambahKategoriModal">Tambah Kategori</button>

    <div class="table-responsive">
        <table class="table mt-3">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nama Kategori</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($categories as $category) : ?>
                <tr>
                    <td><?= $category['id']; ?></td>
                    <td><?= $category['nama_kategori']; ?></td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                data-target="#editKategoriModal<?= $category['id']; ?>">Edit
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                data-target="#deleteKategoriModal<?= $category['id']; ?>">Hapus
                        </button>
                    </td>
                </tr>

                <!-- Modal Edit Kategori -->
                <div class="modal fade" id="editKategoriModal<?= $category['id']; ?>" tabindex="-1"
                     aria-labelledby="editKategoriModalLabel<?= $category['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editKategoriModalLabel<?= $category['id']; ?>">Edit Kategori</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="post">
                                    <input type="hidden" name="edit_id" value="<?= $category['id']; ?>">
                                    <div class="form-group">
                                        <label for="edit_nama_kategori">Nama Kategori:</label>
                                        <input type="text" class="form-control" id="edit_nama_kategori" name="edit_nama_kategori"
                                               value="<?= $category['nama_kategori']; ?>" required>
                                    </div>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary" name="edit_category">Simpan Perubahan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Hapus Kategori -->
                <div class="modal fade" id="deleteKategoriModal<?= $category['id']; ?>" tabindex="-1"
                     aria-labelledby="deleteKategoriModalLabel<?= $category['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteKategoriModalLabel<?= $category['id']; ?>">Hapus Kategori</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Apakah Anda yakin ingin menghapus kategori ini?</p>
                                <form method="post">
                                    <input type="hidden" name="delete_id" value="<?= $category['id']; ?>">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger" name="delete_category">Hapus Kategori</button>
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

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="tambahKategoriModal" tabindex="-1" aria-labelledby="tambahKategoriModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahKategoriModalLabel">Tambah Kategori</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="form-group">
                        <label for="nama_kategori">Nama Kategori:</label>
                        <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
                    </div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" name="add_category">Tambah Kategori</button>
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

    

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
<?php include('inc/foot.php');?>
