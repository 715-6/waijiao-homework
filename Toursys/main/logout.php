<?php
// 检查会话是否已经启动
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_destroy();
header("Location: index.php");
exit;
?>