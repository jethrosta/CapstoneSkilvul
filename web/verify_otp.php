<?php
session_start();

$servername = "localhost";
$username_mysqli = "root"; // Ganti dengan nama pengguna MySQL Anda
$password_mysqli = "your-password"; // Ganti dengan kata sandi MySQL Anda
$dbname_mysqli = "register_login"; // Ganti dengan nama database Anda

// Connect to MySQL server using mysqli
$conn_mysqli = new mysqli($servername, $username_mysqli, $password_mysqli, $dbname_mysqli);

// Check connection
if ($conn_mysqli->connect_error) {
    die("Connection failed: " . $conn_mysqli->connect_error);
}

// Ambil data yang di-submit dari form verify OTP
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$otp = isset($_POST['otp']) ? $_POST['otp'] : '';

// Query untuk mengambil data OTP dari database menggunakan mysqli
$sql_mysqli = "SELECT id, otp, expiry_time FROM otp WHERE email = '$email'";
$result_mysqli = $conn_mysqli->query($sql_mysqli);

if ($result_mysqli->num_rows > 0) {
    // Ada data OTP yang sesuai dengan email yang diberikan
    $row = $result_mysqli->fetch_assoc();
    if ($row['otp'] == $otp && $row['expiry_time'] > time()) {
        // OTP cocok dan masih berlaku
        // TODO: Lakukan tindakan sesuai dengan verifikasi OTP berhasil di sini
        // Misalnya, arahkan pengguna ke halaman selanjutnya atau set sesi untuk login
        // Setelah proses verifikasi selesai, hapus data OTP dari database
        $otp_id = $row['id'];
        $delete_sql = "DELETE FROM otp WHERE id = $otp_id";
        $conn_mysqli->query($delete_sql);
        // Redirect ke halaman dashboard  
        header("Location: homepage.html") ; 
        exit();
    } else {
        header("Location: failed_OTP.html") ; 
        exit();
    }
} else {
    header("Location: failed_OTP.html") ; 
    exit();
}

$conn_mysqli->close();
?>
