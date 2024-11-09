<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $abstrak = $_POST['abstrak'];
    $mahasiswa_id = $_SESSION['id'];

    // Validasi abstrak maksimal 150 kata
    $word_count = str_word_count($abstrak);
    if ($word_count > 150) {
        echo "Abstrak maksimal 150 kata.";
    } else {
        $sql = "INSERT INTO judul_pengajuan (mahasiswa_id, judul, abstrak) VALUES ('$mahasiswa_id', '$judul', '$abstrak')";
        if ($conn->query($sql) === TRUE) {
            echo "Judul berhasil diajukan!";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>

<form method="POST">
    Judul Skripsi: <input type="text" name="judul" required><br>
    Abstrak: <textarea name="abstrak" required></textarea><br>
    <button type="submit">Ajukan Judul</button>
</form>
