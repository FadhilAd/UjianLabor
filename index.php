<?php
session_start();
include 'config.php';

// Periksa apakah pengguna sudah login, jika tidak arahkan ke login atau register
if (!isset($_SESSION['role'])) {
    header("Location: register.php");
    exit();
}

// Ambil role dan nama pengguna
$role = $_SESSION['role'];
$nama = $_SESSION['nama'];
$mahasiswa_id = $_SESSION['id'];

echo "<h1>Selamat Datang, $nama - $role</h1>";

// Role Mahasiswa
if ($role == 'mahasiswa') {
    echo "<h3>Menu Mahasiswa</h3>";
    echo "<ul>
            <li><a href='pengajuan_judul.php'>Pengajuan Judul Skripsi</a></li>
            <li><a href='pembayaran.php'>Pembayaran</a></li>
          </ul>";

    // Menampilkan status pengajuan judul dan pembayaran
    $sql_judul = "SELECT jp.judul, jp.abstrak, jp.status, sp.file_surat_pengantar 
                  FROM judul_pengajuan jp
                  LEFT JOIN surat_pengantar sp ON jp.mahasiswa_id = sp.mahasiswa_id
                  WHERE jp.mahasiswa_id = '$mahasiswa_id'";
    $result_judul = $conn->query($sql_judul);

    if ($result_judul->num_rows > 0) {
        echo "<h4>Pengajuan Judul Skripsi Anda:</h4>";
        echo "<table border='1' cellpadding='10'>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Abstrak</th>
                        <th>Status</th>
                        <th>Surat Pengantar</th>
                        <th>Status Pembayaran</th>
                        <th>Bukti Pembayaran</th>
                    </tr>
                </thead>
                <tbody>";
        while ($judul = $result_judul->fetch_assoc()) {
            $status_judul = ucfirst($judul['status']);
            $surat_siap = isset($judul['file_surat_pengantar']) && !empty($judul['file_surat_pengantar']) ? 
                "<a href='uploads/" . $judul['file_surat_pengantar'] . "' target='_blank'>Lihat Surat</a>" : 
                "Belum Diupload";

            // Status Pembayaran
            $sql_pembayaran = "SELECT * FROM pembayaran WHERE mahasiswa_id = '$mahasiswa_id'";
            $result_pembayaran = $conn->query($sql_pembayaran);
            $status_pembayaran = 'Belum Dibayar'; // Default status pembayaran
            $bukti_pembayaran = 'Belum Diupload';

            if ($result_pembayaran->num_rows > 0) {
                $pembayaran = $result_pembayaran->fetch_assoc();
                $status_pembayaran = ucfirst($pembayaran['status']);
                if (!empty($pembayaran['bukti_pembayaran'])) {
                    $bukti_pembayaran = "<a href='uploads/" . $pembayaran['bukti_pembayaran'] . "' target='_blank'>Lihat Bukti</a>";
                }
            }

            echo "<tr>
                    <td>" . $judul['judul'] . "</td>
                    <td>" . $judul['abstrak'] . "</td>
                    <td>" . $status_judul . "</td>
                    <td>" . $surat_siap . "</td>
                    <td>" . $status_pembayaran . "</td>
                    <td>" . $bukti_pembayaran . "</td>
                  </tr>";
        }
        echo "</tbody></table>";

        // Menampilkan link upload bukti pembayaran jika perlu
        if ($status_judul == 'Diterima' && $status_pembayaran == 'Belum Dibayar') {
            echo "<a href='pembayaran.php'>Upload Bukti Pembayaran</a><br>";
        }
    } else {
        echo "Anda belum mengajukan judul skripsi.<br>";
    }

    echo "<a href='logout.php'>Logout</a>";

// Role Prodi
} elseif ($role == 'prodi') {
    echo "<h3>Menu Prodi</h3>";
    echo "<ul>
            <li><a href='seleksi_judul.php'>Seleksi Judul Skripsi</a></li>
            <li><a href='logout.php'>Logout</a></li>
          </ul>";

    $sql = "SELECT jp.judul, jp.abstrak, jp.status, u.nama, u.nim
            FROM judul_pengajuan jp
            JOIN users u ON jp.mahasiswa_id = u.id
            WHERE jp.status IN ('diterima', 'ditolak', 'belum_diproses')";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h4>Pengajuan Judul Skripsi</h4>";
        echo "<table border='1' cellpadding='10'>
                <thead>
                    <tr>
                        <th>Nama Mahasiswa</th>
                        <th>NIM</th>
                        <th>Judul</th>
                        <th>Abstrak</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>";
        while ($row = $result->fetch_assoc()) {
            $status_judul = ucfirst($row['status']);
            echo "<tr>
                    <td>" . $row['nama'] . "</td>
                    <td>" . $row['nim'] . "</td>
                    <td>" . $row['judul'] . "</td>
                    <td>" . $row['abstrak'] . "</td>
                    <td>" . $status_judul . "</td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "Tidak ada pengajuan judul yang ditemukan.<br>";
    }

// Role Staff Prodi
} elseif ($role == 'staff_prodi') {
    echo "<h3>Menu Staff Prodi</h3>";
    echo "<ul>
            <li><a href='upload_surat_pengantar.php'>Upload Surat Pengantar Pembimbing</a></li>
            <li><a href='rekapitulasi.php'>Laporan Rekapitulasi</a></li>
            <li><a href='logout.php'>Logout</a></li>
          </ul>";
}
?>
