CREATE TABLE buku (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    kode_buku VARCHAR(20) NOT NULL,
    kategori VARCHAR(100) NOT NULL,
    jumlah INT NOT NULL,
    cover_path VARCHAR(255),
    UNIQUE KEY (kode_buku)
);


-- Membuat tabel kategori
CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(255) NOT NULL
);

INSERT INTO kategori (nama_kategori) VALUES ('Fiksi');
INSERT INTO kategori (nama_kategori) VALUES ('Non-Fiksi');
INSERT INTO kategori (nama_kategori) VALUES ('Sains');
INSERT INTO kategori (nama_kategori) VALUES ('Sejarah');


CREATE TABLE peminjaman_user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    buku_id INT NOT NULL,
    nama_peminjam VARCHAR(255) NOT NULL,
    nim_nis VARCHAR(20) NOT NULL,
    judul_buku VARCHAR(255) NOT NULL,
    durasi_peminjaman INT NOT NULL,
    tanggal_peminjaman DATE NOT NULL,
    tanggal_pengembalian DATE,
    status_pengembalian VARCHAR(20) NOT NULL DEFAULT 'Belum Dikembalikan',
    denda INT NOT NULL DEFAULT 0;
    FOREIGN KEY (user_id) REFERENCES pengguna(id),
    FOREIGN KEY (buku_id) REFERENCES buku(id)
);

CREATE TABLE pengguna (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending'
);

