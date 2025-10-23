<?php
// 数据库配置 - 优先从环境变量读取，否则使用默认值
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'toursys';
$username = getenv('DB_USER') ?: 'toursys_user';
$password = getenv('DB_PASSWORD') ?: 'password123';

// 数据库连接选项
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_PERSISTENT         => true, // 启用持久连接
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, $options);
    
    // 设置连接超时
    $pdo->setAttribute(PDO::ATTR_TIMEOUT, 30);
} catch (PDOException $e) {
    error_log("数据库连接失败: " . $e->getMessage());
    die("数据库连接失败，请稍后再试。");
}

// 通用数据库查询函数
function executeQuery($pdo, $sql, $params = []) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("数据库查询错误: " . $e->getMessage() . " SQL: " . $sql);
        throw $e;
    }
}

// 输入清理函数
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
