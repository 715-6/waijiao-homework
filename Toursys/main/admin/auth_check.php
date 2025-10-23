<?php
// 检查会话是否已经启动
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';

// 检查用户是否已登录且为管理员
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}
?>