<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php'; // Ensure the path is correct

use SendGrid\Mail\Mail;

function generateOTP() {
    return rand(100000, 999999); // Atau gunakan metode lain untuk OTP
}

function sendOTP($email, $otp) {
    $sendgrid = new \SendGrid('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');
    
    $email_message = new \SendGrid\Mail\Mail();
    $email_message->setFrom("trufaddapuve-2204@yopmail.com", "Project Capstone Kelompok 14");
    $email_message->setSubject("Your OTP Code");
    $email_message->addTo($email);
    $email_message->addContent("text/plain", "Your OTP code is: " . $otp);

    try {
        $response = $sendgrid->send($email_message);
        return $response->statusCode() == 202;
    } catch (Exception $e) {
        echo 'Caught exception: ' . $e->getMessage();
        return false;
    }
}

session_start();
$email = $_POST['email'];

// Set zona waktu menjadi Waktu Indonesia Barat (WIB)
date_default_timezone_set('Asia/Jakarta');

$expiry_time = time() + 60; // 1 menit expiry time

// Check if the email exists in the users table
$servername = "localhost";
$username_mysqli = "root"; // Ganti dengan nama pengguna MySQL Anda
$password_mysqli = "your-password"; // Ganti dengan kata sandi MySQL Anda
$dbname_mysqli = "register_login"; // Ganti dengan nama database Anda

$conn_mysqli = new mysqli($servername, $username_mysqli, $password_mysqli, $dbname_mysqli);

if ($conn_mysqli->connect_error) {
    die("Connection failed: " . $conn_mysqli->connect_error);
}

// Check if the email is registered
$stmt = $conn_mysqli->prepare("SELECT email FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($existing_email);
$stmt->fetch();
$stmt->close();

if ($existing_email) {
    // Hapus OTP yang sudah kedaluwarsa
    $stmt = $conn_mysqli->prepare("DELETE FROM otp WHERE expiry_time < ?");
    $current_time = time();
    $stmt->bind_param("i", $current_time);
    $stmt->execute();
    $stmt->close();

    $otp = generateOTP();

    // Simpan OTP baru ke database bersama dengan waktu kedaluwarsa
    $stmt = $conn_mysqli->prepare("INSERT INTO otp (email, otp, expiry_time) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $email, $otp, $expiry_time);
    $stmt->execute();
    $stmt->close();

    // Ambil waktu kedaluwarsa dari database berdasarkan email
    $stmt = $conn_mysqli->prepare("SELECT expiry_time FROM otp WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($expiry_timestamp);
    $stmt->fetch();
    $stmt->close();

    if ($expiry_timestamp) {
        // Konversi waktu kedaluwarsa ke dalam format yang lebih mudah dibaca
        $expiry_date = date('Y-m-d H:i:s', $expiry_timestamp);
        echo "Expiry time: " . $expiry_date;
    } else {
        echo "No expiry time found for the email.";
    }

    // Kirim OTP
    if (sendOTP($email, $otp)) {
        header("Location: verify_otp.html");
        exit();
    } else {
        echo "Failed to send OTP.";
        
    }
} else {
    header("Location: email_no_register.html");
    exit();
}

$conn_mysqli->close();
?>
