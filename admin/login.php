<?php

require_once "../config/database.php";

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare(
        "SELECT * FROM admins WHERE username = ?"
    );

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $admin = $stmt->get_result()->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {

        $_SESSION['admin'] = $admin['username'];

        header("Location: dashboard.php");
        exit;

    } else {

        $error = "Invalid username or password.";
    }
}

?>

<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Login - CampusKart</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<div class="login-page">

    <form class="login-box" method="POST">

        <h1>CampusKart Admin</h1>

        <?php if ($error): ?>

            <p class="error">
                <?= $error ?>
            </p>

        <?php endif; ?>

        <input
            type="text"
            name="username"
            placeholder="Username"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="Password"
            required
        >

        <button class="btn" type="submit">
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