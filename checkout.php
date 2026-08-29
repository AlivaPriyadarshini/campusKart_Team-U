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
        href="css/style.css"
    >

</head>


<body>


<header class="navbar">


    <div class="logo">
        Campus<span>Kart</span>
    </div>


    <nav>

        <a href="index.php">
            Home
        </a>


        <a href="product.php">
            Products
        </a>


        <a href="cart.php">
            Cart
        </a>

    </nav>


</header>


<section class="section">


    <div class="checkout">


        <div>

            <h1>Checkout</h1>


            <?php if ($error): ?>

                <p class="error">
                    <?= htmlspecialchars($error) ?>
                </p>

            <?php endif; ?>


            <form method="POST">


                <input
                    type="text"
                    name="name"
                    placeholder="Full Name"
                    value="<?= htmlspecialchars(
                        $_POST['name'] ?? ''
                    ) ?>"
                    required
                >


                <input
                    type="email"
                    name="email"
                    placeholder="Email Address"
                    value="<?= htmlspecialchars(
                        $_POST['email'] ?? ''
                    ) ?>"
                    required
                >


                <input
                    type="text"
                    name="phone"
                    placeholder="Phone Number"
                    value="<?= htmlspecialchars(
                        $_POST['phone'] ?? ''
                    ) ?>"
                >


                <textarea
                    name="address"
                    placeholder="Delivery Address"
                    required
                ><?= htmlspecialchars(
                    $_POST['address'] ?? ''
                ) ?></textarea>


                <button
                    class="btn"
                    type="submit"
                >
                    Place Order
                </button>


            </form>


        </div>


        <div class="order-summary">


            <h2>Order Summary</h2>


            <?php foreach ($cartProducts as $product): ?>


                <p>

                    <?= htmlspecialchars(
                        $product['name']
                    ) ?>

                    × <?= (int)$product['quantity'] ?>

                    = ₹<?= number_format(
                        (float)$product['subtotal'],
                        2
                    ) ?>

                </p>


            <?php endforeach; ?>


            <hr>


            <h2>

                Total:

                ₹<?= number_format(
                    $total,
                    2
                ) ?>

            </h2>


        </div>


    </div>


</section>


</body>

</html>