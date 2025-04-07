<?php
session_start();

// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cart_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Kiểm tra dữ liệu đầu vào
    if (empty($username) || empty($password)) {
        $error = "Vui lòng điền đầy đủ thông tin.";
    } else {
        // Truy vấn người dùng từ cơ sở dữ liệu
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_name = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Kiểm tra mật khẩu
            if (password_verify($password, $user['password_hash'])) {
                // Lưu thông tin người dùng vào session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['user_name'];
                $_SESSION['isAdmin'] = $user['isAdmin'];
                $_SESSION['success_message'] = "Đăng nhập thành công!";

                // Chuyển hướng đến trang index
                header('Location: index.php');
                exit;
            } else {
                $error = "Mật khẩu không đúng.";
            }
        } else {
            $error = "Tên đăng nhập không tồn tại.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
        }

        form div {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #0056b3;
        }

        p {
            margin-top: 15px;
            color: #555;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        p[style="color: red;"] {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Đăng nhập</h1>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <div>
                <label for="username">Tên đăng nhập</label>
                <input type="text" name="username" id="username" placeholder="Điền tên đăng nhập" required>
            </div>
            <div>
                <label for="password">Mật khẩu</label>
                <input type="password" name="password" id="password" placeholder="Điền mật khẩu của bạn" required>
            </div>
            <div>
                <button type="submit">Đăng nhập</button>
            </div>
        </form>
        <p>Chưa có tài khoản? <a href="php.php">Đăng ký ngay</a></p>
    </div>
</body>

</html>