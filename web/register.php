<?php
session_start();

$servername = "localhost";
$username_mysqli = "root"; // Ganti dengan nama pengguna MySQL Anda
$password_mysqli = "your-password"; // Ganti dengan kata sandi MySQL Anda
$dbname_mysqli = "register_login"; // Ganti dengan nama database Anda

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname_mysqli", $username_mysqli, $password_mysqli);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Periksa apakah password dan konfirmasi password cocok
    if ($password !== $confirm_password) {
        echo "<script>alert('Password dan konfirmasi password tidak cocok'); window.location.href = 'register.html';</script>";
        exit();
    }

    // Hash kata sandi sebelum disimpan ke database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Query SQL untuk menyimpan data pengguna ke dalam tabel 'users'
    $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);

    $stmt->execute();

    // Tampilkan popup notifikasi setelah registrasi berhasil
    echo "<script>alert('Registrasi berhasil!'); window.location.href = 'index.html';</script>";
    exit(); // Hentikan eksekusi skrip setelah melakukan redirect
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
