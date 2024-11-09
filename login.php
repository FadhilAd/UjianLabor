<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nim = $_POST['nim'];
    $password = $_POST['password'];

    // Cek user berdasarkan NIM
    $sql = "SELECT * FROM users WHERE nim = '$nim'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password hash
        if (password_verify($password, $user['password'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nim'] = $user['nim'];
            
            header("Location: index.php");
        } else {
            echo "Password salah. Silakan coba lagi.";
        }
    } else {
        echo "NIM tidak ditemukan. Silakan coba lagi.";
    }
}
?>

<form method="POST">
    NIM: <input type="text" name="nim" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Login</button>
</form>
