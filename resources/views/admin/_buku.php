<?php
include 'config.php';

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

function getCategories()
{
    global $koneksi;
    $query = "SELECT DISTINCT nama_kategori FROM kategori";
    $result = $koneksi->query($query);

    if (!$result) {
        die("Error: " . $koneksi->error);
    }

    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['nama_kategori'];
    }

    return $categories;
}

function addBook($judul, $kode_buku, $kategori, $jumlah, $cover)
{
    global $koneksi;
    $judul = $koneksi->real_escape_string($judul);
    $kode_buku = $koneksi->real_escape_string($kode_buku);
    $jumlah = (int)$jumlah;

    $cover_path = '';
    if (!empty($cover['name'])) {
        $cover_dir = 'folder_cover/';
        $cover_path = $cover_dir . basename($cover['name']);
        move_uploaded_file($cover['tmp_name'], $cover_path);
    }

    $query = "INSERT INTO buku (judul, kode_buku, kategori, jumlah, cover_path) VALUES ('$judul', '$kode_buku', '$kategori', $jumlah, '$cover_path')";

    $result = $koneksi->query($query);
    if (!$result) {
        die("Error: " . $koneksi->error);
    }
}

function editBook($id, $judul, $kode_buku, $kategori, $jumlah, $cover)
{
    global $koneksi;
    $judul = $koneksi->real_escape_string($judul);
    $kode_buku = $koneksi->real_escape_string($kode_buku);
    $jumlah = (int)$jumlah;

    $cover_path = '';
    if (!empty($cover['name'])) {
        $cover_dir = 'folder_cover/';
        $cover_path = $cover_dir . basename($cover['name']);
        move_uploaded_file($cover['tmp_name'], $cover_path);
    }

    $query = "UPDATE buku SET judul='$judul', kode_buku='$kode_buku', kategori='$kategori', jumlah=$jumlah, cover_path='$cover_path' WHERE id=$id";

    $result = $koneksi->query($query);
    if (!$result) {
        die("Error: " . $koneksi->error);
    }
}

function deleteBook($id)
{
    global $koneksi;
    $query = "DELETE FROM buku WHERE id = $id";

    $result = $koneksi->query($query);
    if (!$result) {
        die("Error: " . $koneksi->error);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_book'])) {
        $judul = $_POST['judul'];
        $kode_buku = $_POST['kode_buku'];
        $kategori = implode(', ', $_POST['kategori']); // Mengubah array menjadi string dipisahkan koma
        $jumlah = $_POST['jumlah'];
        $cover = $_FILES['cover'];

        addBook($judul, $kode_buku, $kategori, $jumlah, $cover);
    } elseif (isset($_POST['edit_book'])) {
        $id = $_POST['edit_id'];
        $judul = $_POST['edit_judul'];
        $kode_buku = $_POST['edit_kode_buku'];
        $kategori = implode(', ', $_POST['edit_kategori']); // Mengubah array menjadi string dipisahkan koma
        $jumlah = $_POST['edit_jumlah'];
        $cover = $_FILES['edit_cover'];

        editBook($id, $judul, $kode_buku, $kategori, $jumlah, $cover);
    } elseif (isset($_POST['delete_book'])) {
        $id = $_POST['delete_id'];
        deleteBook($id);
    }
}

$books = getAllBooks();
$categories = getCategories();
?>



<?php include('inc/head.php') ?>

<div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Data Buku</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <div class="container-fluid py-4">
    <button type="button" class="btn btn-primary m-2" data-toggle="modal" data-target="#tambahBukuModal">Tambah Buku</button>

    <div class="row">
        <div class="col">
            <div class="table-responsive">
                <table class="table mt-3">
                    <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Kode Buku</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Cover</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($books as $book) : ?>
                        <tr>
                            <td><?= $book['judul']; ?></td>
                            <td><?= $book['kode_buku']; ?></td>
                            <td><?= $book['kategori']; ?></td>
                            <td><?= $book['jumlah']; ?></td>
                            <td>
                                <?php if (!empty($book['cover_path'])) : ?>
                                    <img src="<?= $book['cover_path']; ?>" alt="Cover Buku" style="width: 150px; height: 200px;">
                                <?php else : ?>
                                    <p>Tidak ada cover</p>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                        data-target="#editBukuModal<?= $book['id']; ?>">Edit
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                        data-target="#deleteBukuModal<?= $book['id']; ?>">Hapus
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Edit Buku -->
                        <div class="modal fade" id="editBukuModal<?= $book['id']; ?>" tabindex="-1"
                             aria-labelledby="editBukuModalLabel<?= $book['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editBukuModalLabel<?= $book['id']; ?>">Edit Buku</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="editForm" method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="edit_id" value="<?= $book['id']; ?>">
                                            <div class="form-group">
                                                <label for="edit_judul">Judul:</label>
                                                <input type="text" class="form-control" id="edit_judul" name="edit_judul"
                                                       value="<?= $book['judul']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="edit_kode_buku">Kode Buku:</label>
                                                <input type="text" class="form-control" id="edit_kode_buku" name="edit_kode_buku"
                                                       value="<?= $book['kode_buku']; ?>" required readonly>
                                            </div>
                                            <div class="form-group">
                                            <label for="edit_kategori">Kategori:</label><br>
                                            <?php foreach ($categories as $category) : ?>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" name="edit_kategori[]" value="<?= $category; ?>" <?php echo (in_array($category, explode(', ', $book['kategori']))) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label"><?= $category; ?></label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                            <div class="form-group">
                                                <label for="edit_jumlah">Jumlah:</label>
                                                <input type="number" class="form-control" id="edit_jumlah" name="edit_jumlah"
                                                       value="<?= $book['jumlah']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="edit_cover">Cover Buku:</label>
                                                <input type="file" class="form-control" id="edit_cover" name="edit_cover">
                                                <?php if (!empty($book['cover_path'])) : ?>
                                                    <img src="<?= $book['cover_path']; ?>" alt="Cover Buku"
                                                         style="width: 100px; height: auto;">
                                                <?php else : ?>
                                                    <p>Tidak ada cover</p>
                                                <?php endif; ?>
                                            </div>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary" name="edit_book">Simpan Perubahan</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Hapus Buku -->
                        <div class="modal fade" id="deleteBukuModal<?= $book['id']; ?>" tabindex="-1"
                             aria-labelledby="deleteBukuModalLabel<?= $book['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteBukuModalLabel<?= $book['id']; ?>">Hapus Buku</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Apakah Anda yakin ingin menghapus buku ini?</p>
                                        <form method="post">
                                            <input type="hidden" name="delete_id" value="<?= $book['id']; ?>">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger" name="delete_book">Hapus Buku</button>
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
</div>

<!-- Modal Tambah Buku -->
<div class="modal fade" id="tambahBukuModal" tabindex="-1" aria-labelledby="tambahBukuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahBukuModalLabel">Tambah Buku</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addForm" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="judul">Judul:</label>
                        <input type="text" class="form-control" id="judul" name="judul" required>
                    </div>
                    <div class="form-group">
                        <label for="kode_buku">Kode Buku:</label>
                        <input type="text" class="form-control" id="kode_buku" name="kode_buku" required readonly>
                    </div>
                    <div class="form-group">
                        <label for="kategori">Kategori:</label><br>
                        <?php foreach ($categories as $category) : ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="kategori[]" value="<?= $category; ?>">
                                <label class="form-check-label"><?= $category; ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-group">
                        <label for="jumlah">Jumlah:</label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah" required>
                    </div>
                    <div class="form-group">
                        <label for="cover">Cover Buku:</label>
                        <input type="file" class="form-control" id="cover" name="cover">
                    </div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" name="add_book">Tambah Buku</button>
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

<script>
function generateUniqueBookCode() {
    var randomString = Math.random().toString(36).substring(2, 10).toUpperCase();
    return "BOOK-" + randomString;
}

document.addEventListener('DOMContentLoaded', function() {
    var kodeBukuInput = document.getElementById('kode_buku');
    kodeBukuInput.value = generateUniqueBookCode();
});

function reloadCategories() {
    // AJAX request to get categories
    $.ajax({
        type: 'GET',
        url: 'get_categories.php',
        success: function(data) {
            var categoriesSelect = document.getElementById('kategori');
            categoriesSelect.innerHTML = ''; // Clear existing options

            var editCategoriesSelect = document.getElementById('edit_kategori');
            editCategoriesSelect.innerHTML = ''; // Clear existing options

            // Populate categories in both selects
            data.forEach(function (category) {
                var option = document.createElement('option');
                option.value = category;
                option.text = category;

                categoriesSelect.appendChild(option);

                var editOption = document.createElement('option');
                editOption.value = category;
                editOption.text = category;

                editCategoriesSelect.appendChild(editOption);
            });
        } // Close success function here
    }); // Close AJAX call here
}

// Call the function to load categories on page load
reloadCategories();

// Function to handle form submission using AJAX for adding books
document.getElementById('addForm').addEventListener('submit', function (e) {
    e.preventDefault();

    var form = e.target;
    var formData = new FormData(form);

    // AJAX request to add book
    $.ajax({
        type: 'POST',
        url: form.action,
        data: formData,
        contentType: false,
        processData: false,
        success: function () {
            $('#tambahBukuModal').modal('hide');
            reloadCategories(); // Reload categories after success
            location.reload(); // Reload the page after success
        }
    });
});

// Function to handle form submission using AJAX for editing books
document.getElementById('editForm').addEventListener('submit', function (e) {
    e.preventDefault();

    var form = e.target;
    var formData = new FormData(form);

    // AJAX request to edit book
    $.ajax({
        type: 'POST',
        url: form.action,
        data: formData,
        contentType: false,
        processData: false,
        success: function () {
            $('#editBukuModal').modal('hide');
            reloadCategories(); // Reload categories after success
            location.reload(); // Reload the page after success
        }
    });
});
</script>
<?php include('inc/foot.php');?>
