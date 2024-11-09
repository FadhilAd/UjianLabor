<?php 
session_start();
include 'config.php';

// Pastikan hanya mahasiswa yang bisa mengakses halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: login.php");
    exit();
}

$mahasiswa_id = $_SESSION['id']; // ID mahasiswa yang login

// Ambil status pengajuan judul dari database
$sql_judul = "SELECT status FROM judul_pengajuan WHERE mahasiswa_id = '$mahasiswa_id'";
$result_judul = $conn->query($sql_judul);
$judul = $result_judul->fetch_assoc();

if ($judul && $judul['status'] == 'diterima') {
    // Jika status judul diterima, mahasiswa dapat mengupload bukti pembayaran
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['bukti_pembayaran'])) {
        $bukti_pembayaran = $_FILES['bukti_pembayaran']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($bukti_pembayaran);

        // Pindahkan file ke folder uploads
        if (move_uploaded_file($_FILES['bukti_pembayaran']['tmp_name'], $target_file)) {
            // Cek jika bukti pembayaran sudah ada di database, jika sudah ada, update statusnya
            $sql_check = "SELECT * FROM pembayaran WHERE mahasiswa_id = '$mahasiswa_id'";
            $result_check = $conn->query($sql_check);

            if ($result_check->num_rows > 0) {
                // Jika pembayaran sudah ada, update status menjadi 'sudah_bayar'
                $sql_update = "UPDATE pembayaran SET bukti_pembayaran = '$bukti_pembayaran', status = 'sudah_bayar' 
                               WHERE mahasiswa_id = '$mahasiswa_id'";
                
                if ($conn->query($sql_update) === TRUE) {
                    echo "Bukti pembayaran berhasil diupload dan status pembayaran telah diperbarui menjadi 'sudah_bayar'.";
                } else {
                    echo "Terjadi kesalahan saat memperbarui status pembayaran: " . $conn->error;
                }
            } else {
                // Jika pembayaran belum ada, insert baru
                $sql_insert = "INSERT INTO pembayaran (mahasiswa_id, bukti_pembayaran, status) 
                               VALUES ('$mahasiswa_id', '$bukti_pembayaran', 'sudah_bayar')";
                
                if ($conn->query($sql_insert) === TRUE) {
                    echo "Bukti pembayaran berhasil diupload dan status pembayaran telah diperbarui menjadi 'sudah_bayar'.";
                } else {
                    echo "Terjadi kesalahan saat mengupload bukti pembayaran: " . $conn->error;
                }
            }
        } else {
            echo "Gagal mengupload bukti pembayaran.";
        }
    }

    // Form upload bukti pembayaran
    echo "<h3>Upload Bukti Pembayaran</h3>";
    echo '<form method="POST" enctype="multipart/form-data">
            Bukti Pembayaran: <input type="file" name="bukti_pembayaran" required><br>
            <button type="submit">Upload Pembayaran</button>
          </form>';
} else {
    echo "Pengajuan judul skripsi Anda belum diterima. Anda hanya bisa mengupload bukti pembayaran jika judul skripsi Anda diterima.";
}
?>
