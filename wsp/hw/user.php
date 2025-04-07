<?php
session_start();

require_once 'db_connection.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$search_query = '';
$result = null;

// Handle search functionality
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT user_id, user_name, phone, address FROM users WHERE user_name LIKE ? OR phone LIKE ? OR address LIKE ?");
    $search_term = '%' . $search_query . '%';
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT user_id, user_name, phone, address FROM users");
}

if (!$result) {
    die("Error fetching users: " . $conn->error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_user'])) {
    $id = $_POST['user_id'] ?? '';
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if ($id) {
        $stmt = $conn->prepare("UPDATE users SET user_name=?, phone=?, address=? WHERE user_id=?");
        $stmt->bind_param("sssi", $username, $phone, $address, $id);
    } else {
        if (empty($_POST['password'])) {
            echo "<script>alert('Password is required for new users.'); window.location='user.php';</script>";
            exit;
        }
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (user_name, password_hash, phone, address) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $password, $phone, $address);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Saved successfully!'); window.location='user.php';</script>";
    } else {
        echo "<script>alert('Error: " . htmlspecialchars($stmt->error) . "');</script>";
    }
    $stmt->close();
}
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Deleted successfully!'); window.location='user.php';</script>";
    } else {
        echo "<script>alert('Error deleting: " . htmlspecialchars($stmt->error) . "');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="user.css">
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
        <h2>User Management</h2>
        <!-- Search form -->
        <form method="GET" style="margin-bottom: 20px;">
            <input type="text" name="search" placeholder="Search users..." value="<?= htmlspecialchars($search_query) ?>" class="box">
            <button type="submit" class="btn">Search</button>
        </form>
        <!-- Form to add / edit user -->
        <form method="POST">
            <input type="hidden" name="user_id" id="user_id">
            <input type="text" name="username" id="username" placeholder="Username" required>
            <input type="text" name="phone" id="phone" placeholder="Phone Number" required>
            <input type="text" name="address" id="address" placeholder="Address">
            <input type="password" name="password" id="password" placeholder="Password">
            <button type="submit" name="save_user" class="btn">Save</button>
        </form>

        <!-- User list table -->
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Phone Number</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['user_id']) ?></td>
                    <td><?= htmlspecialchars($row['user_name']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['address']) ?></td>
                    <td>
                        <button class="btn edit-btn" onclick="editUser('<?= htmlspecialchars($row['user_id']) ?>', '<?= htmlspecialchars($row['user_name']) ?>', '<?= htmlspecialchars($row['phone']) ?>', '<?= htmlspecialchars($row['address']) ?>')">✏ Edit</button>
                        <a href="?delete=<?= htmlspecialchars($row['user_id']) ?>" class="btn delete-btn" onclick="return confirm('Confirm delete?');">🗑 Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <div class="buttons">
            <a href="dashboard.php">⬅ Back</a>
        </div>
    </div>

    <script>
        function editUser(id, username, phone, address) {
            document.getElementById("user_id").value = id;
            document.getElementById("username").value = username;
            document.getElementById("phone").value = phone;
            document.getElementById("address").value = address;
            document.getElementById("password").removeAttribute("required");
        }
    </script>
</body>

</html>

<?php $conn->close(); ?>