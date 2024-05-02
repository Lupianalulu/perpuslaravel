<?php
// Ambil nilai parameter q dari URL
$request = isset($_GET['q']) ? $_GET['q'] : '';

// Set path file yang sesuai berdasarkan nilai parameter q
$file = __DIR__ . '/' . $request . '.php';

// Periksa apakah file ada, jika iya, lakukan include
if (file_exists($file)) {
    include $file;
} else {
    // Atau tampilkan pesan atau lakukan yang sesuai jika file tidak ditemukan
    echo '404 Not Found';
}
?>
