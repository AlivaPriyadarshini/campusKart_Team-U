<?php

require_once "config/database.php";


/* ==========================================
   GET PRODUCT ID
   ========================================== */

$id = isset($_GET['id'])
    ? intval($_GET['id'])
    : 0;


/* ==========================================
   SINGLE PRODUCT
   ========================================== */

if ($id > 0) {

    $stmt = $conn->prepare(
        "SELECT * FROM products WHERE id = ?"
    );


    if (!$stmt) {
        die("Database error: " . $conn->error);
    }


    $stmt->bind_param("i", $id);


    if (!$stmt->execute()) {
        die("Unable to load product.");
    }


    $product =
        $stmt->get_result()->fetch_assoc();


    $stmt->close();


    if (!$product) {
        die("Product not found.");
    }


/* ==========================================
   ALL PRODUCTS
   ========================================== */

} else {

    $result = $conn->query(
        "SELECT * FROM products ORDER BY id DESC"
    );


    if (!$result) {
        die("Unable to load products: " . $conn->error);
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


    <title>

        <?= $id > 0
            ? "Product Details"
            : "Products"
        ?>

        - CampusKart

    </title>


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


        <!-- FIXED -->
        <a href="product.php">
            Products
        </a>


        <a href="cart.php">
            Cart 🛒
        </a>

    </nav>


</header>


<?php if ($id > 0): ?>


<!-- ==========================================
     PRODUCT DETAILS
     ========================================== -->


<section class="section">


    <div class="product-detail">


        <?php

        $image = trim(
            $product['image'] ?? ''
        );

        ?>


        <?php if ($image !== ''): ?>

            <img
                src="<?= htmlspecialchars($image) ?>"
                alt="<?= htmlspecialchars(
                    $product['name']
                ) ?>"
            >

        <?php else: ?>

            <div>
                No Image Available
            </div>

        <?php endif; ?>


        <div>


            <span class="category">

                <?= htmlspecialchars(
                    $product['category'] ?? 'Uncategorized'
                ) ?>

            </span>


            <h1>

                <?= htmlspecialchars(
                    $product['name']
                ) ?>

            </h1>


            <p class="price">

                ₹<?= number_format(
                    (float)$product['price'],
                    2
                ) ?>

            </p>


            <p>

                <?= nl2br(
                    htmlspecialchars(
                        $product['description'] ?? ''
                    )
                ) ?>

            </p>


            <p>

                <strong>
                    Stock:
                </strong>


                <?= (int)$product['stock'] ?>

            </p>


            <?php if ((int)$product['stock'] > 0): ?>


                <form
                    action="cart.php"
                    method="POST"
                >


                    <input
                        type="hidden"
                        name="product_id"
                        value="<?= (int)$product['id'] ?>"
                    >


                    <label for="quantity">
                        Quantity
                    </label>


                    <input
                        id="quantity"
                        type="number"
                        name="quantity"
                        value="1"
                        min="1"
                        max="<?= (int)$product['stock'] ?>"
                        class="quantity"
                        required
                    >


                    <button
                        class="btn"
                        type="submit"
                    >
                        Add to Cart
                    </button>


                </form>


            <?php else: ?>


                <p class="out-stock">
                    Out of Stock
                </p>


            <?php endif; ?>


        </div>


    </div>


</section>


<?php else: ?>


<!-- ==========================================
     PRODUCT LIST
     ========================================== -->


<section class="section">


    <h1>
        CampusKart Products
    </h1>


    <?php if ($result->num_rows > 0): ?>


        <div class="product-grid">


            <?php while (
                $product = $result->fetch_assoc()
            ): ?>


                <div class="product-card">


                    <?php

                    $image = trim(
                        $product['image'] ?? ''
                    );

                    ?>


                    <?php if ($image !== ''): ?>


                        <img
                            src="<?= htmlspecialchars($image) ?>"
                            alt="<?= htmlspecialchars(
                                $product['name']
                            ) ?>"
                        >


                    <?php else: ?>


                        <div>
                            No Image Available
                        </div>


                    <?php endif; ?>


                    <div class="product-info">


                        <span class="category">

                            <?= htmlspecialchars(
                                $product['category']
                                ?? 'Uncategorized'
                            ) ?>

                        </span>


                        <h3>

                            <?= htmlspecialchars(
                                $product['name']
                            ) ?>

                        </h3>


                        <p class="price">

                            ₹<?= number_format(
                                (float)$product['price'],
                                2
                            ) ?>

                        </p>


                        <!-- FIXED -->
                        <a
                            href="product.php?id=<?= (int)$product['id'] ?>"
                            class="btn"
                        >
                            View Details
                        </a>


                    </div>


                </div>


            <?php endwhile; ?>


        </div>


    <?php else: ?>


        <p>
            No products available right now.
        </p>


    <?php endif; ?>


</section>


<?php endif; ?>


<footer>

    <p>
        © 2026 CampusKart
    </p>

</footer>


</body>

</html>