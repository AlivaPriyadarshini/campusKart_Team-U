<?php

require_once "config/database.php";

$orderId = isset($_GET['id'])
    ? intval($_GET['id'])
    : 0;

?>

<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Order Successful - CampusKart</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="success-page">

    <div class="success-box">

        <div class="success-icon">
            ✓
        </div>

        <h1>Order Successful!</h1>

        <p>
            Thank you for shopping with CampusKart.
        </p>

        <p>
            Your Order ID:
            <strong>#<?= $orderId ?></strong>
        </p>

        <a href="products.php" class="btn">
            Continue Shopping
        </a>

    </div>

</div>

</body>
</html>