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
        href="../css/style.css"
    >

</head>


<body>


<div class="login-page">


    <form
        class="login-box"
        method="POST"
    >

        <h1>CampusKart Admin</h1>


        <?php if ($error): ?>

            <p class="error">
                <?= htmlspecialchars($error) ?>
            </p>

        <?php endif; ?>


        <input
            type="text"
            name="username"
            placeholder="Username"
            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
            required
        >


        <input
            type="password"
            name="password"
            placeholder="Password"
            required
        >


        <button
            class="btn"
            type="submit"
        >
            Login
        </button>


        <p>
            Default:
            <strong>admin / admin123</strong>
        </p>


    </form>


</div>


</body>

</html>