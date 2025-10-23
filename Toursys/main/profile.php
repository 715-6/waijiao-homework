<?php
// 检查会话是否已经启动
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';
include 'header.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
// 获取用户信息
try {
    $stmt = executeQuery($pdo, "SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // 用户不存在，退出登录
        session_destroy();
        header("Location: login.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("获取用户信息失败: " . $e->getMessage());
    $error = "系统错误，请稍后再试";
}

// 处理错误和成功消息
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

if (isset($_GET['success'])) {
    $success = $_GET['success'];
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 更新用户信息
    if (isset($_POST['update_profile'])) {
        $username = cleanInput($_POST['username']);
        $email = cleanInput($_POST['email']);
        
        // 验证输入
        if (empty($username) || empty($email)) {
            $error = "请填写所有必填字段！";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "邮箱格式不正确！";
        } else {
            try {
                // 检查用户名或邮箱是否已被其他用户使用
                $stmt = executeQuery($pdo, "SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?", 
                                    [$username, $email, $_SESSION['user_id']]);
                if ($stmt->fetch()) {
                    $error = "用户名或邮箱已被其他用户使用！";
                } else {
                    // 更新用户信息
                    $stmt = executeQuery($pdo, "UPDATE users SET username = ?, email = ? WHERE id = ?", 
                                        [$username, $email, $_SESSION['user_id']]);
                    
                    // 更新成功后更新会话信息
                    $_SESSION['username'] = $username;
                    $_SESSION['email'] = $email;
                    
                    $success = "个人信息更新成功！";
                    
                    // 重新获取用户信息
                    $stmt = executeQuery($pdo, "SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
                    $user = $stmt->fetch();
                }
            } catch (PDOException $e) {
                error_log("更新用户信息失败: " . $e->getMessage());
                $error = "更新失败，请稍后再试！";
            }
        }
    }
    
    // 修改密码
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // 验证输入
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = "请填写所有密码字段！";
        } elseif ($new_password !== $confirm_password) {
            $error = "新密码和确认密码不匹配！";
        } elseif (strlen($new_password) < 6) {
            $error = "新密码长度至少6位！";
        } else {
            try {
                // 验证当前密码
                $stmt = executeQuery($pdo, "SELECT password FROM users WHERE id = ?", [$_SESSION['user_id']]);
                $user_data = $stmt->fetch();
                
                if ($user_data && password_verify($current_password, $user_data['password'])) {
                    // 密码正确，更新密码
                    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = executeQuery($pdo, "UPDATE users SET password = ? WHERE id = ?", 
                                        [$hashedPassword, $_SESSION['user_id']]);
                    $success = "密码修改成功！";
                } else {
                    $error = "当前密码不正确！";
                }
            } catch (PDOException $e) {
                error_log("修改密码失败: " . $e->getMessage());
                $error = "密码修改失败，请稍后再试！";
            }
        }
    }
}
?>

<div class="container">
    <div class="auth-form">
        <h2>个人中心</h2>
        
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
        
        <div class="form-group">
            <label>头像:</label>
            <div>
                <img src="avatars/<?php echo htmlspecialchars($user['avatar']); ?>" alt="头像" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
            </div>
            <form method="POST" action="upload_avatar.php" enctype="multipart/form-data" style="margin-top: 15px;">
                <input type="file" name="avatar" accept="image/*" required>
                <button type="submit" class="btn" style="margin-top: 10px;">上传头像</button>
            </form>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">用户名:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">邮箱:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>注册时间:</label>
                <p><?php echo htmlspecialchars($user['created_at']); ?></p>
            </div>
            
            <button type="submit" name="update_profile" class="btn">更新信息</button>
        </form>
        
        <hr style="margin: 30px 0;">
        
        <h3>修改密码</h3>
        <form method="POST">
            <div class="form-group">
                <label for="current_password">当前密码:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">新密码:</label>
                <input type="password" id="new_password" name="new_password" required>
                <small>密码长度至少6位</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">确认新密码:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" name="change_password" class="btn">修改密码</button>
        </form>
        
        <a href="logout.php" class="btn" style="background-color: #e74c3c; margin-top: 20px;">退出登录</a>
    </div>
</div>
<?php include 'footer.php'; ?>
