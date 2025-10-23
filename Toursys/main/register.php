<?php
include 'config.php';
include 'header.php';

// 检查会话是否已经启动
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = cleanInput($_POST['username']);
    $email = cleanInput($_POST['email']);
    $password = $_POST['password'];

    // 验证输入
    if (empty($username) || empty($email) || empty($password)) {
        $error = "请填写所有必填字段！";
    } elseif (strlen($password) < 6) {
        $error = "密码长度至少6位！";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "邮箱格式不正确！";
    } else {
        try {
            // 检查用户名或邮箱是否已存在
            $stmt = executeQuery($pdo, "SELECT id FROM users WHERE username = ? OR email = ?", [$username, $email]);
            if ($stmt->fetch()) {
                $error = "用户名或邮箱已存在！";
            } else {
                // 密码加密
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // 插入新用户
                $stmt = executeQuery($pdo, "INSERT INTO users (username, email, password) VALUES (?, ?, ?)", 
                                    [$username, $email, $hashedPassword]);

                // 注册成功后重定向到登录页面
                header("Location: login.php?registered=1");
                exit;
            }
        } catch (PDOException $e) {
            error_log("注册失败: " . $e->getMessage());
            $error = "注册失败，请稍后再试！";
        }
    }
}
?>

    <div class="container">
        <div class="auth-form">
            <h2>用户注册</h2>

            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="username">用户名:</label>
                    <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">邮箱:</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">密码:</label>
                    <input type="password" id="password" name="password" required>
                    <small>密码长度至少6位</small>
                </div>

                <button type="submit">注册</button>
            </form>

            <p>已有账号？<a href="login.php">立即登录</a></p>
        </div>
    </div>

<?php include 'footer.php'; ?>