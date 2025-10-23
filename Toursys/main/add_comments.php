<?php
// 检查会话是否已经启动
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';
include 'csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 检查用户是否登录
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php?redirect=" . urlencode($_SERVER['HTTP_REFERER']));
        exit;
    }

    // 验证CSRF令牌
    if (!CSRFProtection::validateToken($_POST['csrf_token'])) {
        header("Location: index.php");
        exit;
    }

    $user_id = (int)$_SESSION['user_id'];
    $attraction_id = (int)$_POST['attraction_id'];
    $content = cleanInput($_POST['content']);
    $rating = (int)$_POST['rating'];

    // 验证输入
    if (empty($content) || $rating < 1 || $rating > 5) {
        header("Location: attractions.php?id=" . $attraction_id . "&error=invalid_input");
        exit;
    }

    try {
        // 开始事务
        $pdo->beginTransaction();
        
        // 插入评论
        $stmt = executeQuery($pdo, "INSERT INTO comments (user_id, attraction_id, content, rating) VALUES (?, ?, ?, ?)", 
                            [$user_id, $attraction_id, $content, $rating]);

        // 更新景点的平均评分
        $stmt = executeQuery($pdo, "UPDATE attractions SET rating = (SELECT AVG(rating) FROM comments WHERE attraction_id = ?) WHERE id = ?", 
                            [$attraction_id, $attraction_id]);

        // 提交事务
        $pdo->commit();
        
        // 清除CSRF令牌
        CSRFProtection::clearToken();
        
        // 重定向回景点页面
        header("Location: attractions.php?id=" . $attraction_id . "&success=comment_added");
        exit;
    } catch (PDOException $e) {
        // 回滚事务
        $pdo->rollBack();
        error_log("评论失败: " . $e->getMessage());
        // 重定向回景点页面并显示错误信息
        header("Location: attractions.php?id=" . $attraction_id . "&error=comment_failed&msg=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>