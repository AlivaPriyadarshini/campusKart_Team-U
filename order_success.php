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
        href="css/order_success.css"
    >

</head>


<body>


<!-- ==========================================
     SUCCESS PAGE
========================================== -->

<div class="success-page">


    <!-- ======================================
         SUCCESS CARD
    ====================================== -->

    <div class="success-box">


        <!-- SUCCESS ICON -->

        <div class="success-icon">

            ✓

        </div>



        <!-- MAIN MESSAGE -->

        <span class="success-label">
            ORDER CONFIRMED
        </span>


        <h1>
            Order Successful!
        </h1>


        <p class="success-message">
            Thank you for shopping with CampusKart.
            Your campus essentials are on their way!
        </p>



        <!-- ==================================
             ORDER INFORMATION
        ================================== -->

        <div class="order-info">


            <div class="info-item">

                <span>
                    Order ID
                </span>

                <strong>
                    #<?= (int)$order['id'] ?>
                </strong>

            </div>



            <div class="info-item">

                <span>
                    Total Amount
                </span>

                <strong>
                    ₹<?= number_format(
                        (float)$order['total'],
                        2
                    ) ?>
                </strong>

            </div>


        </div>



        <!-- SUCCESS NOTE -->

        <div class="success-note">

            <span>
                🎓
            </span>

            <p>
                Your order has been placed successfully.
                Thank you for choosing CampusKart!
            </p>

        </div>



        <!-- CONTINUE SHOPPING -->

        <a
            href="product.php"
            class="btn"
        >
            Continue Shopping →
        </a>


        <!-- HOME LINK -->

        <a
            href="index.php"
            class="home-link"
        >
            Back to Home
        </a>


    </div>


</div>


<!-- ==========================================
     FOOTER
========================================== -->

<footer>

    <p>
        © 2026 CampusKart
    </p>

    <span>
        Student E-Commerce Platform
    </span>

</footer>


</body>

</html>