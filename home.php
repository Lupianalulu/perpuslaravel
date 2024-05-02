
<?php
session_start();

include('admin/config.php');

// Fungsi untuk melakukan registrasi
function registerUser($name, $username, $password, $koneksi) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO pengguna (name, username, password) VALUES ('$name', '$username', '$hashedPassword')";
    $result = $koneksi->query($query);

    return $result;
}

// Fungsi untuk melakukan login
function loginUser($username, $password, $koneksi) {
    $query = "SELECT * FROM pengguna WHERE username='$username'";
    $result = $koneksi->query($query);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            return $user;
        }
    }

    return null;
}

// Proses registrasi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = registerUser($name, $username, $password, $koneksi);

    if ($result) {
        echo "Registrasi berhasil!";
    } else {
        echo "Registrasi gagal!";
    }
}

// Fungsi untuk mendapatkan status user
function getUserStatus($username, $koneksi) {
    $query = "SELECT status FROM pengguna WHERE username='$username'";
    $result = $koneksi->query($query);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        return $user['status'];
    }

    return null;
}

// Proses login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = loginUser($username, $password, $koneksi);

    if ($user) {
        $status = getUserStatus($username, $koneksi);

        if ($status == 'Approved') {
            $_SESSION['user_id'] = $user['id'];
            echo "Login berhasil!";
            // Redirect ke halaman setelah login
            header("Location: abuser/user.php");
            exit();
        } elseif ($status == 'Pending') {
            echo "Maaf, pendaftaran Anda masih menunggu persetujuan.";
        } elseif ($status == 'Rejected') {
            echo "Maaf, pendaftaran Anda ditolak.";
        }
    } else {
        echo "Login gagal!";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="public/style.css">
    <title>Login</title>
</head>

<body>

    <div class="container" id="container">
        <div class="form-container sign-up">
            <form method="POST" action="">
                <h1>Registrasi</h1>
                <input type="text" name="name" placeholder="Name" required>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="register">Daftar</button>
            </form>
        </div>
        <div class="form-container sign-in">
            <form method="POST" action="">
                <h1>Masuk</h1>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Masuk</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Selamat Datang</h1>
                    <p>Sudah Punya Akun?</p>
                    <button class="hidden" id="login">Masuk</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Selamat Datang</h1>
                    <p>Belum Punya Akun?</p>
                    <button class="hidden" id="register">Daftar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="public/script.js"></script>
</body>

</html>
