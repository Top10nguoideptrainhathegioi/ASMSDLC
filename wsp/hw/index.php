<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'cart_db');
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We Sell Paint</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7D4pr2IK8iDkCha9iJFw69eU9BIu4SuNWp" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            scroll-behavior: smooth;
            scroll-padding-top: 2rem;
        }

        img {
            width: 100%;
            border-radius: 8px;
        }

        .container {
            max-width: 1200px;
            width: 100%;
            margin: auto;
            padding: 0 1rem;
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5rem 0;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(to right, #ff7eb3, #705eb4);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .cart-icon {
            position: relative;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .cart-icon:hover {
            transform: scale(1.1);
        }

        .cart-icon i {
            font-size: 26px;
        }

        .cart-icon span {
            position: absolute;
            top: -10px;
            right: -12px;
            background: #ff4757;
            color: #fff;
            font-size: 12px;
            padding: 4px 6px;
            border-radius: 50%;
            font-weight: bold;
        }

        .products {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            padding: 3rem 0;
        }

        .product-card {
            background: #fff;
            border-radius: 1.5rem;
            padding: 16px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .product-image {
            width: 100%;
            max-height: 250px;
            height: 100%;
            object-fit: cover;
            object-position: center;
            border-radius: 1rem;
            margin-bottom: 12px;
        }

        .product-name {
            font-size: 1.4rem;
            font-weight: 600;
            color: #333;
            padding: 0 14px;
            margin-bottom: 8px;
        }

        .product-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 14px;
            margin: 8px 0;
        }

        .product-price {
            color: #705eb4;
            font-size: 1.4rem;
            font-weight: 700;
        }

        .product-info p {
            font-size: 0.9rem;
            font-weight: 500;
            color: #666;
        }

        .add-to-cart i {
            background: linear-gradient(to bottom right, #ff7eb3, #705eb4);
            color: #fff;
            font-size: 22px;
            padding: 12px;
            text-align: center;
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .add-to-cart i:hover {
            background: linear-gradient(to bottom right, #705eb4, #ff7eb3);
            transform: scale(1.1);
        }
    </style>
</head>

<body>
    <header>
        <div class="nav container">
            <a href="#" class="logo">We Sell Paint</a>
            <div class="cart-icon" id="cart-icon">
                <i class="ri-shopping-cart-line"></i>
                <span id="cart-count">0</span>
            </div>
            <div>
                <p><a href="myaccount.php">My account</a></p>
            </div>
            <div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>! (<a href="logout.php">Log-Out</a>)</p>
                <?php else: ?>
                    <p><a href="login.php">Log-In</a></p>
                <?php endif; ?>
            </div>
            <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1): ?>
                <div>
                    <p><a href="addproduct.php">Add Products</a></p>
                </div>
                <div>
                    <p><a href="user.php">User Management</a></p>
                </div>
            <?php endif; ?>
        </div>
    </header>
    <div class="products container">
        <?php
        // Truy vấn sản phẩm từ bảng products
        $sql = "SELECT name, price, image FROM products";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Hiển thị từng sản phẩm
            while ($row = $result->fetch_assoc()) {
                echo "
            <div class='product-card'>
                <img src='downloaded_img/{$row['image']}' alt='{$row['name']}' class='product-image'>
                <h2 class='product-name'>{$row['name']}</h2>
                <div class='product-info'>
                    <div class='price-data'>
                        <h2 class='product-price'>" . number_format($row['price'], 2) . " VND</h2>
                        <p>Details</p>
                    </div>
                    <div class='add-to-cart'>
                        <i class='ri-add-line'></i>
                    </div>
                </div>
            </div>";
            }
        } else {
            echo "<p>Không có sản phẩm nào.</p>";
        }
        ?>
    </div>
</body>

</html>