<?php
session_start();
include 'config.php';

// Pastikan hanya prodi yang bisa mengakses halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'prodi') {
    header("Location: login.php");
    exit();
}

echo "<h1>Seleksi Judul Skripsi</h1>";

// Mengambil data pengajuan judul yang belum diproses
$sql = "SELECT jp.id, jp.judul, jp.abstrak, jp.status, u.nama, u.nim
        FROM judul_pengajuan jp
        JOIN users u ON jp.mahasiswa_id = u.id
        WHERE jp.status = 'belum_diproses'";
$result = $conn->query($sql);

// Menampilkan data pengajuan judul
if ($result->num_rows > 0) {
    echo "<div style='width: 80%; margin: auto;'>";
    while ($row = $result->fetch_assoc()) {
        echo "<div style='border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;'>";
        echo "<h3>Judul: " . $row['judul'] . "</h3>";
        echo "<p><strong>Nama Mahasiswa:</strong> " . $row['nama'] . " (NIM: " . $row['nim'] . ")</p>";
        echo "<p><strong>Abstrak:</strong> " . $row['abstrak'] . "</p>";
        
        // Formulir seleksi judul skripsi
        echo "<form method='POST'>
                <label for='status'>Pilih Status:</label>
                <select name='status' required>
                    <option value='diterima'>Terima</option>
                    <option value='ditolak'>Tolak</option>
                </select><br><br>
                
                <label for='alasan'>Alasan (jika ditolak):</label><br>
                <textarea name='alasan' rows='3' placeholder='Isi alasan jika menolak'></textarea><br><br>
                
                <input type='hidden' name='id' value='" . $row['id'] . "'>
                <button type='submit' style='padding: 5px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer;'>Update Status</button>
              </form>";
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "<p style='text-align: center; color: #555;'>Tidak ada pengajuan judul yang belum diproses.</p>";
}

// Proses pengajuan jika ada permintaan POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = $_POST['status'];
    $alasan = $status == 'ditolak' ? $_POST['alasan'] : ''; // Alasan hanya diperlukan jika ditolak
    $id = $_POST['id'];

    $sql_update = "UPDATE judul_pengajuan SET status='$status', alasan='$alasan' WHERE id='$id'";
    if ($conn->query($sql_update) === TRUE) {
        echo "<p style='color: green; text-align: center;'>Status pengajuan judul berhasil diperbarui!</p>";
        // Reload halaman setelah update
        header("Refresh:0");
    } else {
        echo "<p style='color: red; text-align: center;'>Terjadi kesalahan: " . $conn->error . "</p>";
    }
}

?>
