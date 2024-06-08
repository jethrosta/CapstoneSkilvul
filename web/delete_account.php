<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum, arahkan ke halaman login
    header("Location: index.html");
    exit();
}

$servername = "localhost";
$username_mysqli = "root"; // Ganti dengan nama pengguna MySQL Anda
$password_mysqli = ""; // Ganti dengan kata sandi MySQL Anda
$dbname_mysqli = "register_login"; // Ganti dengan nama database Anda

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ambil user_id dari sesi
    $user_id = $_SESSION['user_id'];

    // Query SQL untuk menghapus pengguna dari database
    $sql = "DELETE FROM users WHERE id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Debugging: Periksa apakah baris terpengaruh
    if ($stmt->rowCount() > 0) {
        // Hapus semua data sesi
        session_unset();

        // Hancurkan sesi
        session_destroy();

        // Tampilkan popup notifikasi bahwa akun berhasil dihapus
        echo "<script>alert('Akun berhasil dihapus'); window.location.href = 'index.html';</script>";
    } else {
        echo "<script>alert('Akun tidak ditemukan atau sudah dihapus sebelumnya'); window.location.href = 'dashboard.php';</script>";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>
