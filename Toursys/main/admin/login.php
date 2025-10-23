<?php
// 检查会话是否已经启动
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config.php';
include '../header.php';

// 检查用户是否已登录
if (isset($_SESSION['user_id'])) {
    // 检查是否为管理员
    try {
        $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if ($user && $user['is_admin'] == 1) {
            // 设置管理员会话变量
            $_SESSION['is_admin'] = true;
            header('Location: admin.php');
            exit();
        } else {
            $error = "您没有管理员权限。";
        }
    } catch (PDOException $e) {
        error_log("检查管理员权限时出错: " . $e->getMessage());
        $error = "检查权限时出错。";
    }
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
                // 检查是否为管理员
                if ($user['is_admin'] == 1) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['is_admin'] = true;
                    header('Location: admin.php');
                    exit();
                } else {
                    $error = "您没有管理员权限。";
                }
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
            <h2>管理员登录</h2>

            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
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
        </div>
    </div>

<?php include '../footer.php'; ?>