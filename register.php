<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $nim = $_POST['nim'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Pilih role dari form (mahasiswa/prodi/staff_prodi)

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert data pengguna baru
    $sql = "INSERT INTO users (nama, nim, password, role) VALUES ('$nama', '$nim', '$hashed_password', '$role')";

    if ($conn->query($sql) === TRUE) {
        echo "Registrasi berhasil! <a href='login.php'>Login</a>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<form method="POST">
    Nama: <input type="text" name="nama" required><br>
    NIM: <input type="text" name="nim" required><br>
    Password: <input type="password" name="password" required><br>
    Role: 
    <select name="role" required>
        <option value="mahasiswa">Mahasiswa</option>
        <option value="prodi">Prodi</option>
        <option value="staff_prodi">Staff Prodi</option>
    </select><br>
    <button type="submit">Register</button>
</form>
