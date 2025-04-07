<?php
session_start();

// Kết nối cơ sở dữ liệu
require_once 'db_connection.php';
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Product deleted successfully!'); window.location='managerProdut.php';</script>";
    } else {
        echo "<script>alert('Error deleting product: " . htmlspecialchars($stmt->error) . "');</script>";
    }
    $stmt->close();
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_product'])) {
    $id = $_POST['product_id'] ?? '';
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $quaty = trim($_POST['quaty']);
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'downloaded_img/' . $image;

    if ($id) {
        if (!empty($image)) {
            move_uploaded_file($image_tmp_name, $image_folder);
            $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, quaty = ?, image = ? WHERE id = ?");
            $stmt->bind_param("sdisi", $name, $price, $quaty, $image, $id);
        } else {
            $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, quaty = ? WHERE id = ?");
            $stmt->bind_param("sdii", $name, $price, $quaty, $id);
        }
    } else {
        if (empty($image)) {
            echo "<script>alert('Image is required for new products.'); window.location='managerProdut.php';</script>";
            exit;
        }
        move_uploaded_file($image_tmp_name, $image_folder);
        $stmt = $conn->prepare("INSERT INTO products (name, price, quaty, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdis", $name, $price, $quaty, $image);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Product saved successfully!'); window.location='managerProdut.php';</script>";
    } else {
        echo "<script>alert('Error saving product: " . htmlspecialchars($stmt->error) . "');</script>";
    }
    $stmt->close();
}
$result = $conn->query("SELECT * FROM products");

if (!$result) {
    die("Error fetching products: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #111;
            color: white;
            text-align: center;
        }

        .container {
            width: 80%;
            margin: auto;
            background: #222;
            padding: 20px;
            border-radius: 10px;
        }

        h2 {
            color: #ff69b4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ff69b4;
            padding: 10px;
            text-align: center;
        }

        th {
            background: #ff69b4;
            color: black;
        }

        tr:hover {
            background: #333;
        }

        input,
        button {
            padding: 10px;
            margin: 5px;
        }

        .btn {
            background: #ff69b4;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }

        .edit-btn {
            background: orange;
        }

        .delete-btn {
            background: red;
        }

        form {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        form .box {
            width: 300px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        form .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Manage Products</h2>
        <!-- Form to add / edit product -->
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" id="product_id">
            <input type="text" name="name" id="name" placeholder="Product Name" required>
            <input type="number" step="0.01" name="price" id="price" placeholder="Price" required>
            <input type="number" name="quaty" id="quaty" placeholder="Quantity" required>
            <input type="file" name="image" id="image" accept="image/*">
            <button type="submit" name="save_product" class="btn">Save</button>
        </form>

        <!-- Product list table -->
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars(number_format($row['price'], 2)) ?> VND</td>
                    <td><?= htmlspecialchars($row['quaty']) ?></td>
                    <td><img src="downloaded_img/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" style="width: 100px; height: auto;"></td>
                    <td>
                        <button class="btn edit-btn" onclick="editProduct('<?= htmlspecialchars($row['id']) ?>', '<?= htmlspecialchars($row['name']) ?>', '<?= htmlspecialchars($row['price']) ?>', '<?= htmlspecialchars($row['quaty']) ?>', '<?= htmlspecialchars($row['image']) ?>')">✏ Edit</button>
                        <a href="?delete=<?= htmlspecialchars($row['id']) ?>" class="btn delete-btn" onclick="return confirm('Confirm delete?');">🗑 Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <script>
        function editProduct(id, name, price, quaty, image) {
            document.getElementById("product_id").value = id;
            document.getElementById("name").value = name;
            document.getElementById("price").value = price;
            document.getElementById("quaty").value = quaty;
            document.getElementById("image").removeAttribute("required");
        }
    </script>
</body>

</html>

<?php $conn->close(); ?>