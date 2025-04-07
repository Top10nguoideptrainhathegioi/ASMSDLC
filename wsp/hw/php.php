<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
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
        background-color: #ffffff;
        padding: 20px 30px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
    }

    .form-title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
        text-align: center;
        color: #333333;
    }

    .input-group {
        margin-bottom: 15px;
    }

    .input-group label {
        display: block;
        font-size: 14px;
        margin-bottom: 5px;
        color: #555555;
    }

    .input-group input {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        border: 1px solid #cccccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .input-group input:focus {
        border-color: #007bff;
        outline: none;
    }

    button {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        color: #ffffff;
        background-color: #007bff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    button:hover {
        background-color: #0056b3;
    }

    div p {
        text-align: center;
        margin-top: 15px;
        font-size: 14px;
    }

    div p a {
        color: #007bff;
        text-decoration: none;
    }

    div p a:hover {
        text-decoration: underline;
    }

    div[style="color: red;"] ul {
        padding-left: 20px;
    }

    div[style="color: red;"] li {
        font-size: 14px;
    }

    div[style="color: green;"] p {
        font-size: 14px;
        text-align: center;
    }
</style>

<body>
    <div class="container" id="signup">
        <h1 class="form-title">Đăng Kí</h1>
        <?php
        session_start();

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "cart_db";

        // Tạo kết nối
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Kiểm tra kết nối
        if ($conn->connect_error) {
            die("Kết nối thất bại: " . $conn->connect_error);
        }

        $errors = [];
        $successMessage = '';


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $confirmPassword = trim($_POST['confirm_password'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');

            // Kiểm tra dữ liệu đầu vào
            if (empty($username) || empty($password) || empty($email) || empty($confirmPassword) || empty($phone) || empty($address)) {
                $errors[] = 'Vui lòng nhập đầy đủ thông tin.';
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email không hợp lệ.';
            }

            if ($password !== $confirmPassword) {
                $errors[] = 'Mật khẩu xác nhận không khớp.';
            }

            if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
                $errors[] = 'Mật khẩu phải chứa ít nhất một ký tự đặc biệt.';
            }

            if (empty($errors)) {
                // Mã hóa mật khẩu
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Thêm người dùng vào cơ sở dữ liệu
                $stmt = $conn->prepare("INSERT INTO users (user_name, email, password_hash, phone, address, isAdmin) VALUES (?, ?, ?, ?, ?, ?)");
                $isAdmin = 0; // Mặc định là người dùng thường
                $stmt->bind_param("sssssi", $username, $email, $hashedPassword, $phone, $address, $isAdmin);

                if ($stmt->execute()) {
                    $successMessage = 'Đăng ký thành công!';
                    header('Location: login.php'); // Chuyển hướng đến trang đăng nhập
                    exit;
                } else {
                    $errors[] = 'Đã xảy ra lỗi khi đăng ký. Vui lòng thử lại.';
                }

                $stmt->close();
            }
        }
        ?>

        <?php if (!empty($errors)): ?>
            <div style="color: red;">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Hiển thị thông báo thành công -->
        <?php if ($successMessage): ?>
            <div style="color: green;">
                <p><?php echo htmlspecialchars($successMessage); ?></p>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="input-group">
                <label for="username">Tên đăng nhập</label>
                <input type="text" name="username" id="username" placeholder="Điền tên đăng nhập của bạn" required>
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Điền email của bạn" required>
            </div>
            <div class="input-group">
                <label for="password">Mật khẩu</label>
                <input type="password" name="password" id="password" placeholder="Điền mật khẩu của bạn" required>
            </div>
            <div class="input-group">
                <label for="phone">Số điện thoại</label>
                <input type="text" name="phone" id="phone" placeholder="Điền số điện thoại của bạn" required>
            </div>
            <div class="input-group">
                <label for="address">Địa chỉ</label>
                <input type="text" name="address" id="address" placeholder="Điền địa chỉ của bạn" required>
            </div>
            <div class="input-group">
                <label for="confirm_password">Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Xác nhận lại mật khẩu của bạn" required>
            </div>
            <div>
                <button type="submit">Đăng Ký</button>
            </div>
        </form>
        <div>
            <p>Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
        </div>
    </div>
</body>

</html>