<?php
include 'config.php';

function getAllUsers()
{
    global $koneksi;
    $query = "SELECT * FROM pengguna";
    $result = $koneksi->query($query);

    if (!$result) {
        die("Error: " . $koneksi->error);
    }

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    return $users;
}

function addUser($name, $username, $password)
{
    global $koneksi;
    $name = $koneksi->real_escape_string($name);
    $username = $koneksi->real_escape_string($username);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO pengguna (name, username, password) VALUES ('$name', '$username', '$hashedPassword')";

    $result = $koneksi->query($query);
    if (!$result) {
        die("Error: " . $koneksi->error);
    }
}

function editUser($id, $name, $username, $status)
{
    global $koneksi;
    $name = $koneksi->real_escape_string($name);
    $username = $koneksi->real_escape_string($username);
    $status = $koneksi->real_escape_string($status);

    $query = "UPDATE pengguna SET name='$name', username='$username', status='$status' WHERE id=$id";

    $result = $koneksi->query($query);
    if (!$result) {
        die("Error: " . $koneksi->error);
    }
}

function deleteUser($id)
{
    global $koneksi;
    $query = "DELETE FROM pengguna WHERE id = $id";

    $result = $koneksi->query($query);
    if (!$result) {
        die("Error: " . $koneksi->error);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $name = $_POST['name'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        addUser($name, $username, $password);
    } elseif (isset($_POST['edit_user'])) {
        $id = $_POST['edit_id'];
        $name = $_POST['edit_name'];
        $username = $_POST['edit_username'];
        $status = $_POST['edit_status'];

        editUser($id, $name, $username, $status);
    } elseif (isset($_POST['delete_user'])) {
        $id = $_POST['delete_id'];
        deleteUser($id);
    }
}

$users = getAllUsers();
?>

<?php include('inc/head.php') ?>
<body>
<div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Authors table</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <div class="container mt-5">
    <h2>Data Pengguna</h2>
    <button type="button" class="btn btn-primary m-2" data-toggle="modal" data-target="#tambahUserModal">Tambah Pengguna</button>

    <div class="row">
        <div class="col">
            <div class="table-responsive">
                <table class="table mt-3">
                    <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td><?= $user['name']; ?></td>
                            <td><?= $user['username']; ?></td>
                            <td><?= $user['status']; ?></td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                        data-target="#editUserModal<?= $user['id']; ?>">Edit
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                        data-target="#deleteUserModal<?= $user['id']; ?>">Hapus
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Edit Pengguna -->
                        <!-- Modal Edit Pengguna -->
                        <div class="modal fade" id="editUserModal<?= $user['id']; ?>" tabindex="-1"
                            aria-labelledby="editUserModalLabel<?= $user['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editUserModalLabel<?= $user['id']; ?>">Edit Pengguna</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post">
                                            <input type="hidden" name="edit_id" value="<?= $user['id']; ?>">
                                            <div class="form-group">
                                                <label for="edit_name">Nama:</label>
                                                <input type="text" class="form-control" id="edit_name" name="edit_name"
                                                    value="<?= $user['name']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="edit_username">Username:</label>
                                                <input type="text" class="form-control" id="edit_username" name="edit_username"
                                                    value="<?= $user['username']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="edit_status">Status:</label>
                                                <select class="form-control" id="edit_status" name="edit_status" required>
                                                    <option value="Pending" <?= ($user['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="Approved" <?= ($user['status'] == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                                    <option value="Rejected" <?= ($user['status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                                                </select>
                                            </div>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary" name="edit_user">Simpan Perubahan</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Hapus Pengguna -->
                        <div class="modal fade" id="deleteUserModal<?= $user['id']; ?>" tabindex="-1"
                             aria-labelledby="deleteUserModalLabel<?= $user['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteUserModalLabel<?= $user['id']; ?>">Hapus Pengguna</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Apakah Anda yakin ingin menghapus pengguna ini?</p>
                                        <form method="post">
                                            <input type="hidden" name="delete_id" value="<?= $user['id']; ?>">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger" name="delete_user">Hapus Pengguna</button>
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

<!-- Modal Tambah Pengguna -->
<div class="modal fade" id="tambahUserModal" tabindex="-1" aria-labelledby="tambahUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahUserModalLabel">Tambah Pengguna</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="form-group">
                        <label for="name">Nama:</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" name="add_user">Tambah Pengguna</button>
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
