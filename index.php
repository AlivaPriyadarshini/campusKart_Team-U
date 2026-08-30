<?php
require_once "config/database.php";

$result = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 4");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CampusKart - Student Marketplace</title>

    <link rel="stylesheet" href="css/index.css">
</head>

<body>

    <!-- ================= NAVBAR ================= -->

    <header class="navbar">

        <div class="logo">
            <img src="./image/WhatsApp Image 2026-08-29 at 12.40.25 PM.jpeg"
                 alt="CampusKart Logo">
        </div>

        <nav>
            <a href="index.php">Home</a>
            <a href="product.php">Products</a>
            <a href="#categories">Categories</a>
            <a href="#why-campus">Why CampusKart</a>
            <a href="#how-it-works">How It Works</a>
            <a href="cart.php">Cart 🛒</a>
            <a href="admin/login.php">Admin</a>
        </nav>

    </header>


    <!-- ================= HERO ================= -->

    <section class="hero">

        <img src="./image/hero_image.png"
        alt="Students using CampusKart"
        class="hero-image">

        <div class="hero-overlay"></div>

        <div class="hero-content">

            <span class="hero-badge">
                🎓 Built For Students
            </span>

            <p class="hero-tagline">
                Everything You Need, Right On Campus
            </p>

            <h1>
                Campus Life,
                <span>Made Easier.</span>
            </h1>

            <p class="hero-description">
                Discover affordable stationery, electronics, fashion,
                bags and everyday college essentials — all in one place.
            </p>

            <div class="btn-section">

                <a href="product.php" class="btn">
                    Shop Now →
                </a>

                <a href="#categories" class="btn btn-outline">
                    Explore Categories
                </a>

            </div>

            <div class="hero-features">

                <div>
                    <strong>20+</strong>
                    <small>Products</small>
                </div>

                <div>
                    <strong>🎓</strong>
                    <small>Student Focused</small>
                </div>

                <div>
                    <strong>💰</strong>
                    <small>Affordable</small>
                </div>

            </div>

        </div>

    </section>


    <!-- ================= INTRO ================= -->

    <section class="intro-section">

        <div class="intro-content">

            <span class="section-label">
                WELCOME TO CAMPUSKART
            </span>

            <h2>
                Your Campus.
                <span>Your Marketplace.</span>
            </h2>

            <p>
                CampusKart is a student-focused online marketplace created
                to make college shopping simple, convenient and affordable.
                Whether you need something for your classes, your hostel,
                your style or your everyday campus life, we've got you covered.
            </p>

        </div>

    </section>


    <!-- ================= WHY CAMPUSKART ================= -->

    <section class="why-campus" id="why-campus">

        <div class="section-heading">

            <span class="section-label">
                WHY CAMPUSKART
            </span>

            <h2>
                Shopping Made For
                <span>Student Life</span>
            </h2>

            <p>
                Everything you need, designed around the way students
                actually live, study and shop.
            </p>

        </div>


        <div class="why-grid">

            <div class="why-card">

                <div class="why-icon">
                    🎓
                </div>

                <h3>Made For Students</h3>

                <p>
                    Products selected specifically for college students
                    and everyday campus life.
                </p>

            </div>


            <div class="why-card">

                <div class="why-icon">
                    💰
                </div>

                <h3>Student-Friendly Prices</h3>

                <p>
                    Get useful products at affordable prices that fit
                    comfortably within a student budget.
                </p>

            </div>


            <div class="why-card">

                <div class="why-icon">
                    🛍️
                </div>

                <h3>Everything In One Place</h3>

                <p>
                    Stationery, electronics, fashion, bags and college
                    essentials — all available from one marketplace.
                </p>

            </div>


            <div class="why-card">

                <div class="why-icon">
                    ⚡
                </div>

                <h3>Simple & Convenient</h3>

                <p>
                    Browse products, add them to your cart and checkout
                    with just a few simple steps.
                </p>

            </div>

        </div>

    </section>


    <!-- ================= CATEGORIES ================= -->

    <section class="categories-section" id="categories">

        <div class="section-heading">

            <span class="section-label">
                EXPLORE
            </span>

            <h2>
                Shop By
                <span>Category</span>
            </h2>

            <p>
                Find exactly what you need for your college journey.
            </p>

        </div>


        <div class="category-grid">

            <a href="product.php" class="category-card">

                <div class="category-icon">
                    🎒
                </div>

                <div>
                    <h3>Bags</h3>
                    <p>Backpacks & everyday carry</p>
                </div>

                <span>→</span>

            </a>


            <a href="product.php" class="category-card">

                <div class="category-icon">
                    💻
                </div>

                <div>
                    <h3>Electronics</h3>
                    <p>Gadgets & accessories</p>
                </div>

                <span>→</span>

            </a>


            <a href="product.php" class="category-card">

                <div class="category-icon">
                    👕
                </div>

                <div>
                    <h3>Fashion</h3>
                    <p>College style & comfort</p>
                </div>

                <span>→</span>

            </a>


            <a href="product.php" class="category-card">

                <div class="category-icon">
                    📚
                </div>

                <div>
                    <h3>Stationery</h3>
                    <p>Study & writing essentials</p>
                </div>

                <span>→</span>

            </a>


            <a href="product.php" class="category-card">

                <div class="category-icon">
                    ☕
                </div>

                <div>
                    <h3>College Essentials</h3>
                    <p>Everyday campus products</p>
                </div>

                <span>→</span>

            </a>

        </div>

    </section>


    <!-- ================= POPULAR PRODUCTS ================= -->

    <section class="section popular-section">

        <div class="section-heading">

            <span class="section-label">
                STUDENT FAVORITES
            </span>

            <h2>
                Popular
                <span>Products</span>
            </h2>

            <p>
                Check out some of the products students are loving.
            </p>

        </div>


        <div class="product-grid">

            <?php while ($product = $result->fetch_assoc()): ?>

                <div class="product-card">

                    <div class="product-image-wrapper">

                        <img
                            src="<?= htmlspecialchars($product['image']) ?>"
                            alt="<?= htmlspecialchars($product['name']) ?>">

                        <span class="product-badge">
                            Popular
                        </span>

                    </div>


                    <div class="product-info">

                        <span class="category">
                            <?= htmlspecialchars($product['category']) ?>
                        </span>

                        <h3>
                            <?= htmlspecialchars($product['name']) ?>
                        </h3>

                        <p class="price">
                            ₹<?= number_format($product['price'], 2) ?>
                        </p>

                        <a
                            href="products.php?id=<?= $product['id'] ?>"
                            class="btn">
                            View Product →
                        </a>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>


        <div class="center-btn">

            <a href="product.php" class="btn">
                View All Products →
            </a>

        </div>

    </section>


    <!-- ================= PROMO ================= -->

    <section class="promo-section">

        <div class="promo-content">

            <span class="section-label">
                CAMPUSKART
            </span>

            <h2>
                Great Products.
                <span>Great Prices.</span>
            </h2>

            <p>
                We believe students shouldn't have to compromise between
                quality and affordability. CampusKart brings useful
                products together at prices made for student life.
            </p>

            <a href="product.php" class="btn">
                Start Shopping →
            </a>

        </div>

    </section>


    <!-- ================= HOW IT WORKS ================= -->

    <section class="how-section" id="how-it-works">

        <div class="section-heading">

            <span class="section-label">
                HOW IT WORKS
            </span>

            <h2>
                Shopping Made
                <span>Simple</span>
            </h2>

            <p>
                Get what you need in just a few easy steps.
            </p>

        </div>


        <div class="steps">

            <div class="step">

                <div class="step-number">
                    01
                </div>

                <div class="step-icon">
                    🔎
                </div>

                <h3>Browse</h3>

                <p>
                    Explore our collection of student-friendly products.
                </p>

            </div>


            <div class="step">

                <div class="step-number">
                    02
                </div>

                <div class="step-icon">
                    🛍️
                </div>

                <h3>Choose</h3>

                <p>
                    Select the products and quantities you need.
                </p>

            </div>


            <div class="step">

                <div class="step-number">
                    03
                </div>

                <div class="step-icon">
                    🛒
                </div>

                <h3>Add To Cart</h3>

                <p>
                    Add your favorite products to your shopping cart.
                </p>

            </div>


            <div class="step">

                <div class="step-number">
                    04
                </div>

                <div class="step-icon">
                    ✅
                </div>

                <h3>Checkout</h3>

                <p>
                    Enter your details and place your order.
                </p>

            </div>

        </div>

    </section>


    <!-- ================= ABOUT ================= -->

    <section class="about-section">

        <div class="about-image">

            <img
                src="./image/about.jpeg"
                alt="Students using CampusKart">

        </div>


        <div class="about-content">

            <span class="section-label">
                ABOUT CAMPUSKART
            </span>

            <h2>
                Built Around
                <span>Student Life.</span>
            </h2>

            <p>
                CampusKart was created with one simple idea:
                make everyday college shopping easier.
            </p>

            <p>
                From the notebook you need for tomorrow's class to the
                headphones you use while studying, CampusKart brings
                essential products together in one convenient marketplace.
            </p>


            <div class="about-points">

                <div>
                    ✓ Affordable products
                </div>

                <div>
                    ✓ Student-focused marketplace
                </div>

                <div>
                    ✓ Simple shopping experience
                </div>

                <div>
                    ✓ Wide range of campus essentials
                </div>

            </div>


            <a href="product.php" class="btn">
                Explore CampusKart →
            </a>

        </div>

    </section>


    <!-- ================= FINAL CTA ================= -->

    <section class="final-cta">

        <div>

            <span class="section-label">
                READY?
            </span>

            <h2>
                Make Your Campus Life
                <span>A Little Easier.</span>
            </h2>

            <p>
                Discover products made for students and start shopping
                smarter with CampusKart.
            </p>

            <a href="product.php" class="btn">
                Shop CampusKart →
            </a>

        </div>

    </section>


    <!-- ================= FOOTER ================= -->

    <footer>

        <div class="footer-container">


            <div class="footer-brand">

                <div class="logo">
                    <img
                        src="./image/WhatsApp Image 2026-08-29 at 12.40.25 PM.jpeg"
                        alt="CampusKart">
                </div>

                <p>
                    Your student marketplace for affordable,
                    useful and everyday campus essentials.
                </p>

            </div>


            <div class="footer-column">

                <h3>Quick Links</h3>

                <a href="index.php">Home</a>
                <a href="product.php">Products</a>
                <a href="#categories">Categories</a>
                <a href="#why-campus">Why CampusKart</a>

            </div>


            <div class="footer-column">

                <h3>CampusKart</h3>

                <a href="#how-it-works">How It Works</a>
                <a href="#">About Us</a>
                <a href="#">Contact</a>
                <a href="cart.php">Shopping Cart</a>

            </div>


            <div class="footer-column">

                <h3>Contact</h3>

                <p>📧 support@campuskart.com</p>
                <p>🎓 Student Marketplace</p>
                <p>📍 Your Campus</p>

            </div>


        </div>


        <div class="footer-bottom">

            <p>
                © 2026 CampusKart | Student E-Commerce Platform
            </p>

            <p>
                Built for Students. Built for Campus.
            </p>

        </div>

    </footer>


    <script src="js/script.js"></script>

</body>

</html>