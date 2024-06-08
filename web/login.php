<?php
session_start();

$servername = "localhost";
$dbname_mysqli = "register_login"; // Ganti dengan nama database Anda$username_mysqli = "kelompok14_towersaydo"; // Ganti dengan nama pengguna MySQL Anda
$password_mysqli = "your-password"; // Ganti dengan kata sandi MySQL Anda
$username_mysqli = 'root';

// Connect to MySQL server using mysqli
$conn_mysqli = new mysqli($servername, $username_mysqli, $password_mysqli, $dbname_mysqli);

// Check connection
if ($conn_mysqli->connect_error) {
    die("Connection failed: " . $conn_mysqli->connect_error);
}

// Ambil data yang di-submit dari form login
$username_email = isset($_POST['username_email']) ? $_POST['username_email'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Query untuk mengambil data pengguna dari database menggunakan mysqli
$sql_mysqli = "SELECT id, username, email, password FROM users WHERE username = '$username_email' OR email = '$username_email'";
$result_mysqli = $conn_mysqli->query($sql_mysqli);

if ($result_mysqli->num_rows > 0) {
    // Ada pengguna dengan username/email yang diberikan
    $row = $result_mysqli->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        // Password cocok, login berhasil
        // Simpan data pengguna ke dalam sesi
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['email'] = $row['email'];
        // Arahkan pengguna ke halaman dashboard setelah login berhasil
        header("Location: input_otp.html");
        exit();
    } else {
        // Password tidak cocok, login gagal
        echo "<script>alert('Password salah'); window.location.href = 'index.html';</script>";
    }
} else {
    // Tidak ada pengguna dengan username/email yang diberikan
    echo "<script>alert('Pengguna tidak ditemukan'); window.location.href = 'index.html';</script>";
}

$conn_mysqli->close();
?>
