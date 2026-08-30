<?php

require_once "../config/database.php";


/* ==========================================
   LOGOUT
   ========================================== */

if (isset($_GET['logout'])) {

    $_SESSION = [];

    session_destroy();

    header("Location: login.php");
    exit;
}


/* ==========================================
   ALREADY LOGGED IN
   ========================================== */

if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit;
}


$error = "";


/* ==========================================
   HANDLE LOGIN
   ========================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';


    /* ==========================================
       VALIDATION
       ========================================== */

    if ($username === '' || $password === '') {

        $error = "Please enter username and password.";

    } else {

        /* ==========================================
           FIND ADMIN
           ========================================== */

        $stmt = $conn->prepare(
            "SELECT id, username, password
             FROM admins
             WHERE username = ?
             LIMIT 1"
        );


        if (!$stmt) {

            $error = "Database error. Please try again.";

        } else {

            $stmt->bind_param(
                "s",
                $username
            );


            if (!$stmt->execute()) {

                $error = "Login failed. Please try again.";

            } else {

                $admin = $stmt
                    ->get_result()
                    ->fetch_assoc();


                /* ==========================================
                   VERIFY PASSWORD
                   ========================================== */

                if (
                    $admin &&
                    password_verify(
                        $password,
                        $admin['password']
                    )
                ) {

                    /* Prevent session fixation */
                    session_regenerate_id(true);

                    $_SESSION['admin'] = $admin['username'];

                    header("Location: dashboard.php");
                    exit;

                } else {

                    $error = "Invalid username or password.";
                }
            }

            $stmt->close();
        }
    }
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Login - CampusKart</title>

    <link
        rel="stylesheet"
        href="../css/login.css"
    >

</head>


<body>


<!-- ==========================================
     LOGIN PAGE
     ========================================== -->

<div class="login-page">


    <!-- ==========================================
         LOGIN CARD
         ========================================== -->

    <form
        class="login-box"
        method="POST"
    >


        <!-- Logo -->

        <div class="admin-logo">
            <img src="../image/WhatsApp Image 2026-08-29 at 12.40.25 PM.jpeg"
                alt="CampusKart Logo">
        </div>


        <!-- Heading -->

        <p class="login-label">
            CAMPUSKART ADMIN
        </p>


        <h1>
            Welcome Back 👋
        </h1>


        <p class="login-description">
            Sign in to manage your CampusKart marketplace.
        </p>



        <!-- ==========================================
             ERROR MESSAGE
             ========================================== -->

        <?php if ($error): ?>

            <p class="error">
                <?= htmlspecialchars($error) ?>
            </p>

        <?php endif; ?>



        <!-- ==========================================
             USERNAME
             ========================================== -->

        <div class="input-group">

            <label for="username">
                Username
            </label>

            <input
                id="username"
                type="text"
                name="username"
                placeholder="Enter your username"
                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                required
            >

        </div>



        <!-- ==========================================
             PASSWORD
             ========================================== -->

        <div class="input-group">

            <label for="password">
                Password
            </label>

            <input
                id="password"
                type="password"
                name="password"
                placeholder="Enter your password"
                required
            >

        </div>



        <!-- Login Button -->

        <button
            class="btn"
            type="submit"
        >
            Login to Dashboard
        </button>



        <!-- Demo Login -->

        <div class="demo-login">

            <p>
                Demo Login
            </p>

            <span>
                Username: <strong>admin</strong>
            </span>

            <span>
                Password: <strong>admin123</strong>
            </span>

        </div>



        <!-- Back to Website -->

        <a
            href="../index.php"
            class="back-home"
        >
            ← Back to CampusKart
        </a>


    </form>


</div>


</body>

</html>