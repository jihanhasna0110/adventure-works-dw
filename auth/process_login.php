<?php
session_start();

// KONFIG DATABASE
$host     = "localhost";
$user     = "root";
$password = "";
$dbname   = "adventureworks_dw";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Kalau form dikirim via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validasi sederhana
    if ($username === '' || $password === '') {
        $_SESSION['login_error'] = "Username atau password tidak boleh kosong.";
        header("Location: login.php"); // Redirect ke login.php di folder yang sama
        exit;
    }

    // Hash password dengan MD5
    $password_md5 = md5($password);

    // Query cek user (CASE INSENSITIVE untuk username)
    $sql  = "SELECT UserID, Username, FullName, Role 
             FROM users 
             WHERE LOWER(Username) = LOWER(?) AND Password = ? 
             LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        $_SESSION['login_error'] = "Error pada query: " . mysqli_error($conn);
        header("Location: login.php");
        exit;
    }
    
    mysqli_stmt_bind_param($stmt, "ss", $username, $password_md5);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Login sukses → set session
        $_SESSION['loggedin'] = true;
        $_SESSION['UserID']   = $row['UserID'];
        $_SESSION['Username'] = $row['Username'];
        $_SESSION['FullName'] = $row['FullName'];
        $_SESSION['Role']     = $row['Role'];

        // Arahkan ke halaman utama (naik 1 level ke parent directory)
        header("Location: ../pages/dashboard.php");
        exit;
    } else {
        // Login gagal
        $_SESSION['login_error'] = "Username atau password salah. Pastikan password sudah di-hash MD5 di database.";
        header("Location: login.php");
        exit;
    }
} else {
    // Jika akses langsung tanpa POST, redirect ke login
    header("Location: login.php");
    exit;
}

mysqli_close($conn);
?>