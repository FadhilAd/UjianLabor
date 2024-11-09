<?php
session_start();
include 'config.php';

// Pastikan pengguna sudah login dan memiliki role staff prodi
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'staff_prodi') {
    header("Location: login.php");
    exit();
}

// Menampilkan tabel yang berisi semua judul beserta statusnya
echo "<h4>Laporan Rekapitulasi</h4>";

$sql = "SELECT u.nama, u.nim, jp.judul, jp.status AS status_judul, p.status AS status_pembayaran, s.file_surat_pengantar
        FROM users u
        JOIN judul_pengajuan jp ON u.id = jp.mahasiswa_id
        LEFT JOIN pembayaran p ON u.id = p.mahasiswa_id
        LEFT JOIN surat_pengantar s ON u.id = s.mahasiswa_id"; // Semua status: diterima, ditolak, belum diproses

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Tampilkan tabel untuk semua judul dan status
    echo "<table border='1' cellpadding='10'>
            <tr>
                <th>Nama Mahasiswa</th>
                <th>NIM</th>
                <th>Judul</th>
                <th>Status Judul</th>
                <th>Status Pembayaran</th>
                <th>Surat Pengantar</th>
            </tr>";

    // Menampilkan data dari hasil query
    while ($row = $result->fetch_assoc()) {
        $status_judul = ucfirst($row['status_judul']);
        $status_pembayaran = ucfirst($row['status_pembayaran']);
        // Cek apakah file surat pengantar ada, jika ada tampilkan link
        if ($row['file_surat_pengantar']) {
            $file_surat = "<a href='uploads/" . $row['file_surat_pengantar'] . "' target='_blank'>Lihat Surat</a>";
        } else {
            $file_surat = "Belum Diupload";
        }

        echo "<tr>
                <td>" . $row['nama'] . "</td>
                <td>" . $row['nim'] . "</td>
                <td>" . $row['judul'] . "</td>
                <td>" . $status_judul . "</td>
                <td>" . $status_pembayaran . "</td>
                <td>" . $file_surat . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "Tidak ada data pengajuan judul yang ditemukan.<br>";
}

echo "<br><a href='logout.php'>Logout</a>";
?>
