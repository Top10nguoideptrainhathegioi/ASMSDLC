<?php

@include 'config.php';
if (isset($_POST['add_product'])) {
    $product_name = htmlspecialchars(trim($_POST['product_name']));
    $product_price = htmlspecialchars(trim($_POST['product_price']));
    $product_quaty = htmlspecialchars(trim($_POST['product_quaty']));
    $product_image = $_FILES['product_image']['name'];
    $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
    $product_image_folder = 'downloaded_img/' . $product_image;

    // Kiểm tra các trường nhập liệu
    if (empty($product_name) || empty($product_price) || empty($product_image) || empty($product_quaty)) {
        echo "<script>alert('Please fill all the fields');</script>";
    } elseif (!is_numeric($product_price) || $product_price <= 0) {
        echo "<script>alert('Please enter a valid price');</script>";
    } else {
        // Kiểm tra định dạng file ảnh
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $file_extension = strtolower(pathinfo($product_image, PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            echo "<script>alert('Only JPG, JPEG, and PNG files are allowed');</script>";
        } else {
            // Chuẩn bị câu lệnh SQL với đúng số lượng giá trị
            $stmt = $con->prepare("INSERT INTO products (name, price, quaty, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sdis", $product_name, $product_price, $product_quaty, $product_image);

            if ($stmt->execute()) {
                if (move_uploaded_file($product_image_tmp_name, $product_image_folder)) {
                    echo "<script>alert('Product added successfully');</script>";
                    echo "<script>window.location.href = 'index.php';</script>";
                } else {
                    echo "<script>alert('Failed to upload image');</script>";
                }
            } else {
                echo "<script>alert('Failed to add product');</script>";
            }
            $stmt->close();
        }
        // Display delete button for each product
        $result = $con->query("SELECT * FROM products");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div>";
                echo "<p>Product: " . htmlspecialchars($row['name']) . " - Price: " . htmlspecialchars($row['price']) . "</p>";
                echo "<form method='post' action=''>";
                echo "<input type='hidden' name='delete_product_id' value='" . htmlspecialchars($row['id']) . "'>";
                echo "<input type='submit' name='delete_product' value='Delete' class='btn'>";
                echo "</form>";
                echo "</div>";
            }
        }

        // Handle delete product request
        if (isset($_POST['delete_product'])) {
            $delete_product_id = intval($_POST['delete_product_id']);
            $stmt = $con->prepare("DELETE FROM products WHERE id = ?");
            $stmt->bind_param("i", $delete_product_id);

            if ($stmt->execute()) {
                echo "<script>alert('Product deleted successfully');</script>";
                echo "<script>window.location.href = 'addproduct.php';</script>";
            } else {
                echo "<script>alert('Failed to delete product');</script>";
            }
            $stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 16px;
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h3 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: #007bff;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        .box {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .box:focus {
            border-color: #007bff;
            outline: none;
        }

        .btn {
            padding: 10px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .alert {
            text-align: center;
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3>Add New Product</h3>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <input type="text" placeholder="Enter Product Name" name="product_name" class="box" required>
            <input type="number" placeholder="Enter Product Price" name="product_price" class="box" required>
            <input type="number" placeholder="Nhập số lượng " name="product_quaty" class="box" required>
            <input type="file" accept="image/png, image/jpg, image/jpeg" name="product_image" class="box" required>
            <input type="submit" class="btn" name="add_product" value="Add Product">
        </form>
    </div>
</body>

</html>