<?php
include 'config.php';
include 'header.php';
ob_start();
// 检查会话是否已经启动
if (session_status() === PHP_SESSION_NONE) {
    ob_end_clean(); 
    session_start();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = cleanInput($_POST['username']);
    $password = $_POST['password'];

    // 验证输入
    if (empty($username) || empty($password)) {
        $error = "请填写所有必填字段！";
    } else {
        try {
            $stmt = executeQuery($pdo, "SELECT * FROM users WHERE username = ? OR email = ?", [$username, $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['is_admin'] = $user['is_admin']; // 存储管理员状态

                // 重定向到首页或之前访问的页面
                if (isset($_GET['redirect'])) {
                    header("Location: " . $_GET['redirect']);
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $error = "用户名或密码错误！";
            }
        } catch (PDOException $e) {
            error_log("登录失败: " . $e->getMessage());
            $error = "登录失败，请稍后再试！";
        }
    }
}
?>

    <div class="container">
        <div class="auth-form">
            <h2>用户登录</h2>

            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <?php if (isset($_GET['registered'])): ?>
                <p class="success">注册成功！请登录您的账户。</p>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="username">用户名或邮箱:</label>
                    <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">密码:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit">登录</button>
            </form>

            <p>还没有账号？<a href="register.php">立即注册</a></p>
        </div>
    </div>

<?php include 'footer.php'; ?>
