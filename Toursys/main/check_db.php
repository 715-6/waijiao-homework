<?php
require_once 'config.php';

try {
    echo "Connected successfully\n";
    
    // 检查表是否存在
    $stmt = $pdo->query("SHOW TABLES");
    echo "Tables in database:\n";
    while ($row = $stmt->fetch()) {
        print_r($row);
    }
    
    // 检查attractions表中的记录数
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM attractions");
    $result = $stmt->fetch();
    echo "Number of attractions: " . $result['count'] . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>