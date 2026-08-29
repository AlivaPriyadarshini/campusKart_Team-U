<?php

require_once "config/database.php";

if (empty($_SESSION['cart'])) {
    header("Location: products.php");
    exit;
}

$total = 0;
$cartProducts = [];

foreach ($_SESSION['cart'] as $id => $quantity) {

    $stmt = $conn->prepare(
        "SELECT * FROM products WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $product = $stmt->get_result()->fetch_assoc();

    if ($product) {

        $product['quantity'] = $quantity;

        $product['subtotal'] =
            $product['price'] * $quantity;

        $total += $product['subtotal'];

        $cartProducts[] = $product;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if ($name && $email && $address) {

        $stmt = $conn->prepare("
            INSERT INTO orders
            (customer_name, email, phone, address, total)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssssd",
            $name,
            $email,
            $phone,
            $address,
            $total
        );

        $stmt->execute();

        $orderId = $conn->insert_id;


        foreach ($cartProducts as $product) {

            $stmt = $conn->prepare("
                INSERT INTO order_items
                (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "iiid",
                $orderId,
                $product['id'],
                $product['quantity'],
                $product['price']
            );

            $stmt->execute();
        }


        $_SESSION['cart'] = [];

        header(
            "Location: order_success.php?id=" . $orderId
        );

        exit;
    }
}

?>

<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Checkout - CampusKart</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<header class="navbar">

    <div class="logo">
        Campus<span>Kart</span>
    </div>

    <nav>
        <a href="index.php">Home</a>
        <a href="products.php">Products</a>
        <a href="cart.php">Cart</a>
    </nav>

</header>


<section class="section">

    <div class="checkout">

        <div>

            <h1>Checkout</h1>

            <form method="POST">

                <input
                    type="text"
                    name="name"
                    placeholder="Full Name"
                    required
                >

                <input
                    type="email"
                    name="email"
                    placeholder="Email Address"
                    required
                >

                <input
                    type="text"
                    name="phone"
                    placeholder="Phone Number"
                >

                <textarea
                    name="address"
                    placeholder="Delivery Address"
                    required
                ></textarea>

                <button class="btn" type="submit">
                    Place Order
                </button>

            </form>

        </div>


        <div class="order-summary">

            <h2>Order Summary</h2>

            <?php foreach ($cartProducts as $product): ?>

                <p>
                    <?= htmlspecialchars($product['name']) ?>

                    × <?= $product['quantity'] ?>

                    = ₹<?= number_format(
                        $product['subtotal'],
                        2
                    ) ?>
                </p>

            <?php endforeach; ?>

            <hr>

            <h2>
                Total:
                ₹<?= number_format($total, 2) ?>
            </h2>

        </div>

    </div>

</section>

</body>
</html>