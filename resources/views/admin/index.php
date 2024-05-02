<?php
session_start();

include('config.php');

// Fungsi untuk melakukan login admin
function loginAdmin($username, $password, $koneksi) {
    $query = "SELECT * FROM admin_login WHERE username='$username' AND password='$password'";
    $result = $koneksi->query($query);

    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        return $admin;
    }

    return null;
}

// Proses login admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $admin = loginAdmin($username, $password, $koneksi);

    if ($admin) {
        $_SESSION['admin_id'] = $admin['id'];
        echo "Login berhasil!";
        // Redirect ke halaman setelah login
        header("Location: home");
        exit();
    } else {
        echo "Login gagal! Periksa kembali username dan password Anda.";
    }
}

$koneksi->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../public/style.css">
    <title>Login Admin</title>
</head>

<body>

    <div class="container" id="container">
        <div class="form-container sign-in">
            <form method="POST" action="">
                <h1>Masuk Admin</h1>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Masuk</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-right">
                    <h1>Selamat Datang</h1>
                </div>
            </div>
        </div>
    </div>

    <script src="../public/script.js"></script>
</body>

</html>
