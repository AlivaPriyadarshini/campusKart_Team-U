<?php

require_once "config/database.php";


/* ==========================================
   GET ORDER ID
   ========================================== */

$orderId = isset($_GET['id'])
    ? intval($_GET['id'])
    : 0;


/* ==========================================
   VALIDATE ORDER
   ========================================== */

if ($orderId <= 0) {
    header("Location: product.php");
    exit;
}


/* Check whether order exists */

$stmt = $conn->prepare(
    "SELECT id, customer_name, total, created_at
     FROM orders
     WHERE id = ?"
);


if (!$stmt) {
    die("Database error: " . $conn->error);
}


$stmt->bind_param("i", $orderId);


if (!$stmt->execute()) {
    die("Unable to verify order.");
}


$order = $stmt
    ->get_result()
    ->fetch_assoc();


$stmt->close();


/* Order doesn't exist */

if (!$order) {
    header("Location: product.php");
    exit;
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

    <title>Order Successful - CampusKart</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>


<body>


<div class="success-page">


    <div class="success-box">


        <div class="success-icon">
            ✓
        </div>


        <h1>
            Order Successful!
        </h1>


        <p>
            Thank you for shopping with CampusKart.
        </p>


        <p>
            Your order has been placed successfully.
        </p>


        <p>

            Your Order ID:

            <strong>
                #<?= (int)$order['id'] ?>
            </strong>

        </p>


        <p>

            Total:

            <strong>
                ₹<?= number_format(
                    (float)$order['total'],
                    2
                ) ?>
            </strong>

        </p>


        <!-- Correct filename -->

        <a
            href="product.php"
            class="btn"
        >
            Continue Shopping
        </a>


    </div>


</div>


</body>

</html>