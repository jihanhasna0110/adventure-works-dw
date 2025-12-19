<?php
session_start();

// Kalau sudah login, redirect ke dashboard
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: ../pages/dashboard.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../config/database.php';

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validasi sederhana
    if ($username === '' || $password === '') {
        $error = "Username atau password tidak boleh kosong.";
    } else {
        $database = new Database();
        $db       = $database->getConnection();

        // Ambil user berdasarkan username (case-insensitive)
        $sql = "
            SELECT 
                UserID,
                Username,
                Password,
                FullName,
                Role
            FROM users
            WHERE LOWER(Username) = LOWER(:username)
            LIMIT 1
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // ===================================================
            // VERIFIKASI PASSWORD DENGAN MD5
            // ===================================================
            // Kolom 'Password' di database harus berisi md5(plain_password)
            if (md5($password) === $user['Password']) {
                // Login sukses → set session
                $_SESSION['loggedin'] = true;
                $_SESSION['UserID']   = $user['UserID'];
                $_SESSION['Username'] = $user['Username'];
                $_SESSION['FullName'] = $user['FullName'];
                $_SESSION['Role']     = $user['Role'];

                // (opsional) kalau masih ada yang pakai user_id lama
                $_SESSION['user_id']  = $user['UserID'];

                header("Location: ../pages/dashboard.php");
                exit();
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - Adventure Works Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block" style="background: white; position: relative; height: 450px;">
                                <img src="../assets/img/undraw_posting_photo.svg" alt="Adventure Works" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); max-width: 80%; height: auto;">
                            </div>

                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Adventure Works Dashboard</h1>
                                        <p class="mb-4">Data Warehouse & OLAP System</p>
                                    </div>

                                    <?php if ($error): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                    <form class="user" method="POST" action="">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user"
                                                name="username" placeholder="Username" required autofocus>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user"
                                                name="password" placeholder="Password" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <small class="text-muted">
                                            Demo Account:<br>

                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../assets/js/sb-admin-2.min.js"></script>
</body>

</html>