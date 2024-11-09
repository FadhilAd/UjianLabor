<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'staff_prodi') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mahasiswa_id = $_POST['mahasiswa_id'];
    $file_surat = $_FILES['surat_pengantar']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($file_surat);
    move_uploaded_file($_FILES['surat_pengantar']['tmp_name'], $target_file);

    $sql = "INSERT INTO surat_pengantar (mahasiswa_id, file_surat_pengantar) VALUES ('$mahasiswa_id', '$file_surat')";
    if ($conn->query($sql) === TRUE) {
        echo "Surat Pengantar Pembimbing berhasil diupload!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <label for="mahasiswa_id">Pilih Mahasiswa:</label>
    <select name="mahasiswa_id">
        <?php
        $sql = "SELECT id, nama FROM users WHERE role = 'mahasiswa'";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['nama'] . "</option>";
        }
        ?>
    </select><br>
    Upload Surat Pengantar Pembimbing: <input type="file" name="surat_pengantar" required><br>
    <button type="submit">Upload Surat Pengantar</button>
</form>
