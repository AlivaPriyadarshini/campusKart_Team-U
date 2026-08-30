<?php

session_start();

$host = "localhost";
$user = "root";
$password = "";
$database = "campuskart";

$conn = new mysqli($host, $user, $password);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS campuskart");
$conn->select_db($database);

$conn->query("
    CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(500),
        category VARCHAR(100),
        stock INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(150) NOT NULL,
        email VARCHAR(150) NOT NULL,
        phone VARCHAR(30),
        address TEXT NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL
    )
");

/* Default admin account */
$checkAdmin = $conn->query("SELECT id FROM admins LIMIT 1");

if ($checkAdmin->num_rows == 0) {
    $adminPassword = password_hash("admin123", PASSWORD_DEFAULT);

    $stmt = $conn->prepare(
        "INSERT INTO admins (username, password) VALUES (?, ?)"
    );

    $username = "admin";
    $stmt->bind_param("ss", $username, $adminPassword);
    $stmt->execute();
}

/* Sample products */
$checkProducts = $conn->query("SELECT id FROM products LIMIT 1");

if ($checkProducts->num_rows == 0) {

    $products = [

        [
            "Campus Backpack",
            "Durable backpack suitable for college students.",
            899,
            "https://images.unsplash.com/photo-1553062407-98eeb64c6a62",
            "Bags",
            20
        ],

        [
            "College Hoodie",
            "Comfortable cotton hoodie for everyday campus life.",
            699,
            "https://images.unsplash.com/photo-1556821840-3a63f95609a7",
            "Fashion",
            15
        ],

        [
            "Wireless Headphones",
            "Comfortable wireless headphones for study and entertainment.",
            1299,
            "https://images.unsplash.com/photo-1505740420928-5e560c06d30e",
            "Electronics",
            10
        ],

        [
            "Study Notebook",
            "Premium ruled notebook for college notes.",
            149,
            "https://images.unsplash.com/photo-1531346680769-a1d79b57de5b",
            "Stationery",
            50
        ],

        [
            "Laptop Sleeve",
            "Protective laptop sleeve suitable for students.",
            499,
            "https://images.unsplash.com/photo-1496181133206-80ce9b88a853",
            "Electronics",
            25
        ],

        [
            "Mechanical Keyboard",
            "Compact mechanical keyboard perfect for coding and studying.",
            1599,
            "https://images.unsplash.com/photo-1587829741301-dc798b83add3",
            "Electronics",
            12
        ],

        [
            "Wireless Mouse",
            "Smooth and comfortable wireless mouse for laptops and desktops.",
            599,
            "https://images.unsplash.com/photo-1527814050087-3793815479db",
            "Electronics",
            30
        ],

        [
            "College T-Shirt",
            "Comfortable cotton t-shirt for everyday college wear.",
            399,
            "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab",
            "Fashion",
            35
        ],

        [
            "Water Bottle",
            "Reusable stainless steel water bottle for campus.",
            349,
            "https://images.unsplash.com/photo-1602143407151-7111542de6e8",
            "College Essentials",
            40
        ],

        [
            "Coffee Mug",
            "Stylish ceramic mug perfect for students and coffee lovers.",
            249,
            "https://images.unsplash.com/photo-1514228742587-6b1558fcf93a",
            "College Essentials",
            30
        ],

        [
            "Ball Pen Pack",
            "Pack of smooth-writing ball pens for everyday college use.",
            99,
            "https://images.unsplash.com/photo-1583485088034-697b5bc54ccd",
            "Stationery",
            100
        ],

        [
            "Highlighter Set",
            "Set of colorful highlighters for notes and study.",
            129,
            "https://images.unsplash.com/photo-1586953208448-b95a79798f07",
            "Stationery",
            60
        ],

        [
            "Sticky Notes",
            "Colorful sticky notes for reminders, study notes and planning.",
            79,
            "https://images.unsplash.com/photo-1586282391129-76a6df230234",
            "Stationery",
            80
        ],

        [
            "College Backpack Mini",
            "Compact everyday backpack for books and personal essentials.",
            649,
            "https://images.unsplash.com/photo-1622560480605-d83c853bc5c3",
            "Bags",
            25
        ],

        [
            "Canvas Tote Bag",
            "Lightweight reusable tote bag for books and daily college use.",
            299,
            "https://images.unsplash.com/photo-1590874103328-eac38a683ce7",
            "Bags",
            30
        ],

        [
            "Sports Cap",
            "Lightweight casual cap suitable for college and outdoor activities.",
            249,
            "https://images.unsplash.com/photo-1521369909029-2afed882baee",
            "Fashion",
            20
        ],

        [
            "Smart Watch",
            "Stylish smartwatch for fitness tracking and daily notifications.",
            1999,
            "https://images.unsplash.com/photo-1523275335684-37898b6baf30",
            "Electronics",
            8
        ],

        [
            "USB Flash Drive 64GB",
            "Portable 64GB USB drive for storing college projects and files.",
            449,
            "https://images.unsplash.com/photo-1627123424574-724758594e93",
            "Electronics",
            20
        ],

        [
            "Desk Organizer",
            "Compact organizer for pens, stationery and study accessories.",
            299,
            "https://images.unsplash.com/photo-1494438639946-1ebd1d20bf85",
            "Stationery",
            25
        ],

        [
            "Calculator",
            "Scientific calculator suitable for college mathematics and programming courses.",
            549,
            "https://images.unsplash.com/photo-1587145820266-a5951ee6f620",
            "College Essentials",
            18
        ]

    ];
    $stmt = $conn->prepare("
        INSERT INTO products
        (name, description, price, image, category, stock)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($products as $product) {
        $stmt->bind_param(
            "ssdssi",
            $product[0],
            $product[1],
            $product[2],
            $product[3],
            $product[4],
            $product[5]
        );

        $stmt->execute();
    }
}
?>