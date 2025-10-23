<?php
session_start();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>旅游信息网</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <div class="container">
        <div class="logo">
            <h1><a href="index.php">旅游信息网</a></h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">首页</a></li>
                <li><a href="attractions.php">景点大全</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">个人中心</a></li>
                    <li><a href="logout.php">退出</a></li>
                <?php else: ?>
                    <li><a href="login.php">登录</a></li>
                    <li><a href="register.php">注册</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
