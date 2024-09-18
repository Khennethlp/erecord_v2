<?php require 'process/login.php';

if (isset($_SESSION['username'])) {
    if ($_SESSION['role'] == 'admin_reviewer') {
        header('location: page/admin_reviewer/list_of_req.php');
    } elseif ($_SESSION['role'] == 'admin') {
        header('location: page/admin/dashboard.php');
    } elseif ($_SESSION['role'] == 'qc') {
        header('location: page/qc/dashboard.php');
    } elseif ($role == 'hrd_approver') {
        header('location: page/hrd_approver/list_of_req.php');
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Record System | Login</title>
    <link rel="alternate icon" href="dist/img/logo.ico" type="image/x-icon" />
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="dist/css/font.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<style>
    .card {
        margin: auto;
    }
</style>

<body>
    <section class="vh-100">
        <div class="container py-5 h-100">
            <div class="row d-flex align-items-center justify-content-center h-100">
                <div class="col-md-8 col-lg-7 col-xl-6">
                    <img src="dist/img/logo.png" class="img-fluid" alt="ERECORD ICON" style="height:350px;">
                </div>
                <div class="col-md-7 col-lg-5 col-xl-5 offset-xl-1">
                    <form action="" method="POST" id="login_form">
                        <!-- Email input -->
                        <div data-mdb-input-init class="form-outline mb-4">
                            <label class="form-label" for="">Username</label>
                            <input type="text" id="username" name="username" class="form-control form-control-lg" />
                        </div>

                        <!-- Password input -->
                        <div data-mdb-input-init class="form-outline mb-4">
                            <label class="form-label" for="">Password</label>
                            <input type="password" id="password" name="password" class="form-control form-control-lg" />
                        </div>

                        <!-- Submit button -->
                        <button type="submit" name="login_btn" class="btn btn-lg btn-block" style="background-color: #275DAD; color:#fff;">Sign in</button>
                        <hr>
                        <a class="btn btn-lg btn-block" style="background-color: #335145; color:#fff;" href="#!" role="button">
                            Work Instruction
                        </a>
                        <a class="btn btn-lg btn-block" style="background-color: #B83D43; color:#fff;" href="#!" role="button">
                            Back to viewer</a>

                    </form>
                </div>
            </div>
        </div>
    </section>


    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
</body>

</html>