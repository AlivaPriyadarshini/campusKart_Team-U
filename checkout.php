<?php

require_once "config/database.php";


/* ==========================================
   CHECK CART
   ========================================== */

if (empty($_SESSION['cart'])) {
    header("Location: product.php");
    exit;
}


$total = 0;
$cartProducts = [];


/* ==========================================
   LOAD CART PRODUCTS
   ========================================== */

foreach ($_SESSION['cart'] as $id => $quantity) {

    $id = intval($id);
    $quantity = intval($quantity);

    if ($id <= 0 || $quantity < 1) {
        continue;
    }


    $stmt = $conn->prepare(
        "SELECT * FROM products WHERE id = ?"
    );


    if (!$stmt) {
        die("Database error: " . $conn->error);
    }


    $stmt->bind_param("i", $id);


    if (!$stmt->execute()) {
        die("Unable to load product: " . $stmt->error);
    }


    $product = $stmt
        ->get_result()
        ->fetch_assoc();


    $stmt->close();


    if (!$product) {

        unset($_SESSION['cart'][$id]);

        continue;
    }


    /* ==========================================
       CHECK STOCK
       ========================================== */

    if ($product['stock'] < 1) {

        unset($_SESSION['cart'][$id]);

        continue;
    }


    if ($quantity > (int)$product['stock']) {

        $quantity = (int)$product['stock'];

        $_SESSION['cart'][$id] = $quantity;
    }


    if ($quantity < 1) {
        continue;
    }


    /* ==========================================
       CALCULATE SUBTOTAL
       ========================================== */

    $product['quantity'] = $quantity;

    $product['subtotal'] =
        (float)$product['price'] * $quantity;


    $total += $product['subtotal'];

    $cartProducts[] = $product;
}


/* ==========================================
   CART BECAME EMPTY
   ========================================== */

if (empty($cartProducts)) {

    $_SESSION['cart'] = [];

    header("Location: product.php");
    exit;
}


/* ==========================================
   HANDLE ORDER
   ========================================== */

$error = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');


    /* ==========================================
       VALIDATION
       ========================================== */

    if ($name === '') {

        $error = "Please enter your full name.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif ($address === '') {

        $error = "Please enter your delivery address.";

    } else {


        /* ==========================================
           START TRANSACTION
           ========================================== */

        $conn->begin_transaction();


        try {


            /* ==========================================
               CHECK STOCK AGAIN
               ========================================== */

            foreach ($cartProducts as $product) {

                $stmt = $conn->prepare("
                    SELECT stock
                    FROM products
                    WHERE id = ?
                    FOR UPDATE
                ");


                if (!$stmt) {
                    throw new Exception(
                        "Unable to check product stock."
                    );
                }


                $stmt->bind_param(
                    "i",
                    $product['id']
                );


                if (!$stmt->execute()) {
                    throw new Exception(
                        "Unable to check product stock."
                    );
                }


                $stockData =
                    $stmt->get_result()->fetch_assoc();


                $stmt->close();


                if (!$stockData) {
                    throw new Exception(
                        "Product no longer exists."
                    );
                }


                if (
                    (int)$stockData['stock']
                    < $product['quantity']
                ) {

                    throw new Exception(
                        "Not enough stock available for " .
                        $product['name'] . "."
                    );
                }
            }


            /* ==========================================
               CREATE ORDER
               ========================================== */

            $stmt = $conn->prepare("
                INSERT INTO orders
                (
                    customer_name,
                    email,
                    phone,
                    address,
                    total
                )
                VALUES (?, ?, ?, ?, ?)
            ");


            if (!$stmt) {
                throw new Exception(
                    "Unable to create order."
                );
            }


            $stmt->bind_param(
                "ssssd",
                $name,
                $email,
                $phone,
                $address,
                $total
            );


            if (!$stmt->execute()) {
                throw new Exception(
                    "Unable to create order."
                );
            }


            $orderId = $conn->insert_id;

            $stmt->close();


            /* ==========================================
               INSERT ORDER ITEMS + REDUCE STOCK
               ========================================== */

            foreach ($cartProducts as $product) {


                /* Insert order item */

                $stmt = $conn->prepare("
                    INSERT INTO order_items
                    (
                        order_id,
                        product_id,
                        quantity,
                        price
                    )
                    VALUES (?, ?, ?, ?)
                ");


                if (!$stmt) {
                    throw new Exception(
                        "Unable to save order item."
                    );
                }


                $stmt->bind_param(
                    "iiid",
                    $orderId,
                    $product['id'],
                    $product['quantity'],
                    $product['price']
                );


                if (!$stmt->execute()) {
                    throw new Exception(
                        "Unable to save order item."
                    );
                }


                $stmt->close();


                /* Reduce stock */

                $stmt = $conn->prepare("
                    UPDATE products
                    SET stock = stock - ?
                    WHERE id = ?
                    AND stock >= ?
                ");


                if (!$stmt) {
                    throw new Exception(
                        "Unable to update stock."
                    );
                }


                $stmt->bind_param(
                    "iii",
                    $product['quantity'],
                    $product['id'],
                    $product['quantity']
                );


                if (!$stmt->execute()) {
                    throw new Exception(
                        "Unable to update stock."
                    );
                }


                if ($stmt->affected_rows !== 1) {
                    throw new Exception(
                        "Stock changed. Please try again."
                    );
                }


                $stmt->close();
            }


            /* ==========================================
               EVERYTHING SUCCESSFUL
               ========================================== */

            $conn->commit();


            /* Empty cart */

            $_SESSION['cart'] = [];


            /* Redirect */

            header(
                "Location: order_success.php?id=" .
                $orderId
            );

            exit;


        } catch (Exception $e) {


            /* Undo everything */

            $conn->rollback();


            $error = $e->getMessage();
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

    <title>Checkout - CampusKart</title>

    <link
        rel="stylesheet"
        href="css/checkout.css"
    >

</head>


<body>


<!-- ==========================================
     NAVBAR
========================================== -->

<header class="navbar">


    <div class="logo">
        <img src="./image/WhatsApp Image 2026-08-29 at 12.40.25 PM.jpeg"
            alt="CampusKart Logo">
    </div>


    <nav>

        <a href="index.php">
            Home
        </a>


        <a href="product.php">
            Products
        </a>


        <a href="cart.php">
            Cart 🛒
        </a>

    </nav>


</header>



<!-- ==========================================
     CHECKOUT PAGE
========================================== -->

<section class="checkout-page">


    <!-- PAGE HEADER -->

    <div class="checkout-header">

        <span class="section-label">
            CAMPUSKART CHECKOUT
        </span>

        <h1>
            Complete Your Order
        </h1>

        <p>
            Just a few details and your campus essentials
            will be ready to go.
        </p>

    </div>



    <!-- ==========================================
         CHECKOUT LAYOUT
    =========================================== -->

    <div class="checkout-layout">


        <!-- ======================================
             CUSTOMER DETAILS
        ====================================== -->

        <div class="checkout-form-box">


            <div class="form-heading">

                <div class="form-icon">
                    👤
                </div>

                <div>

                    <h2>
                        Delivery Details
                    </h2>

                    <p>
                        Enter your information below
                    </p>

                </div>

            </div>


            <?php if ($error): ?>

                <p class="error">
                    <?= htmlspecialchars($error) ?>
                </p>

            <?php endif; ?>


            <form method="POST">


                <!-- FULL NAME -->

                <div class="input-group">

                    <label for="name">
                        Full Name
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        placeholder="Enter your full name"
                        value="<?= htmlspecialchars(
                            $_POST['name'] ?? ''
                        ) ?>"
                        required
                    >

                </div>



                <!-- EMAIL -->

                <div class="input-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        placeholder="Enter your email address"
                        value="<?= htmlspecialchars(
                            $_POST['email'] ?? ''
                        ) ?>"
                        required
                    >

                </div>



                <!-- PHONE -->

                <div class="input-group">

                    <label for="phone">
                        Phone Number
                    </label>

                    <input
                        id="phone"
                        type="text"
                        name="phone"
                        placeholder="Enter your phone number"
                        value="<?= htmlspecialchars(
                            $_POST['phone'] ?? ''
                        ) ?>"
                    >

                </div>



                <!-- ADDRESS -->

                <div class="input-group">

                    <label for="address">
                        Delivery Address
                    </label>

                    <textarea
                        id="address"
                        name="address"
                        placeholder="Enter your complete delivery address"
                        required
                    ><?= htmlspecialchars(
                        $_POST['address'] ?? ''
                    ) ?></textarea>

                </div>



                <!-- ORDER BUTTON -->

                <button
                    class="btn place-order-btn"
                    type="submit"
                >

                    Place Order →

                </button>


            </form>


        </div>



        <!-- ======================================
             ORDER SUMMARY
        ====================================== -->

        <div class="order-summary">


            <div class="summary-top">

                <span class="section-label">
                    YOUR ORDER
                </span>

                <h2>
                    Order Summary
                </h2>

            </div>



            <!-- PRODUCTS -->

            <div class="summary-products">


                <?php foreach ($cartProducts as $product): ?>


                    <div class="summary-product">


                        <div class="summary-product-info">

                            <h3>

                                <?= htmlspecialchars(
                                    $product['name']
                                ) ?>

                            </h3>

                            <p>

                                Quantity:
                                <?= (int)$product['quantity'] ?>

                            </p>

                        </div>


                        <strong>

                            ₹<?= number_format(
                                (float)$product['subtotal'],
                                2
                            ) ?>

                        </strong>


                    </div>


                <?php endforeach; ?>


            </div>



            <!-- SUMMARY DETAILS -->

            <div class="summary-details">


                <div class="summary-row">

                    <span>
                        Items
                    </span>

                    <span>
                        Included
                    </span>

                </div>


                <div class="summary-row">

                    <span>
                        Delivery
                    </span>

                    <span class="free">
                        Student Friendly
                    </span>

                </div>


            </div>



            <hr>



            <!-- TOTAL -->

            <div class="summary-total">

                <span>
                    Total Amount
                </span>

                <strong>

                    ₹<?= number_format(
                        $total,
                        2
                    ) ?>

                </strong>

            </div>



            <!-- SECURITY MESSAGE -->

            <div class="secure-message">

                <span>
                    🔒
                </span>

                <p>
                    Your order details are handled
                    securely.
                </p>

            </div>


        </div>


    </div>


</section>



<!-- ==========================================
     CHECKOUT BENEFITS
========================================== -->

<section class="checkout-benefits">


    <div class="benefit">


        <div class="benefit-icon">
            🎓
        </div>


        <div>

            <h3>
                Built For Students
            </h3>

            <p>
                Shopping made simple for campus life.
            </p>

        </div>


    </div>



    <div class="benefit">


        <div class="benefit-icon">
            💰
        </div>


        <div>

            <h3>
                Student Friendly
            </h3>

            <p>
                Affordable products for everyday needs.
            </p>

        </div>


    </div>



    <div class="benefit">


        <div class="benefit-icon">
            🔒
        </div>


        <div>

            <h3>
                Simple & Secure
            </h3>

            <p>
                A straightforward checkout experience.
            </p>

        </div>


    </div>


</section>



<!-- ==========================================
     FOOTER
========================================== -->

<footer>


    <div class="footer-container">


        <div class="footer-brand">

            <h2>
                Campus<span>Kart</span>
            </h2>

            <p>
                Your student marketplace for affordable,
                useful and reliable campus essentials.
            </p>

        </div>


        <div class="footer-column">

            <h3>
                Quick Links
            </h3>

            <a href="index.php">
                Home
            </a>

            <a href="product.php">
                Products
            </a>

            <a href="cart.php">
                Cart
            </a>

        </div>


        <div class="footer-column">

            <h3>
                Categories
            </h3>

            <p>
                Stationery
            </p>

            <p>
                Electronics
            </p>

            <p>
                Fashion
            </p>

            <p>
                Bags
            </p>

        </div>


        <div class="footer-column">

            <h3>
                CampusKart
            </h3>

            <p>
                Student Focused
            </p>

            <p>
                Affordable
            </p>

            <p>
                Easy Shopping
            </p>

            <p>
                Quality Products
            </p>

        </div>


    </div>



    <div class="footer-bottom">

        <p>
            © 2026 CampusKart
        </p>

        <p>
            Student E-Commerce Platform
        </p>

    </div>


</footer>


</body>

</html>