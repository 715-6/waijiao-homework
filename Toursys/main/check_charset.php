<?php
require_once 'config.php';

try {
    echo "Connected successfully\n";
    
    // 检查数据库字符集
    $stmt = $pdo->query("SHOW VARIABLES LIKE 'character_set%';");
    echo "Character set settings:\n";
    while ($row = $stmt->fetch()) {
        echo $row['Variable_name'] . " = " . $row['Value'] . "\n";
    }
    
    // 检查表的字符集
    echo "\nTable character sets:\n";
    $stmt = $pdo->query("SELECT TABLE_NAME, TABLE_COLLATION FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'toursys';");
    while ($row = $stmt->fetch()) {
        echo $row['TABLE_NAME'] . " = " . $row['TABLE_COLLATION'] . "\n";
    }
    
    // 检查attractions表中的数据
    echo "\nAttractions data:\n";
    $stmt = $pdo->query("SELECT id, name, description FROM attractions LIMIT 3;");
    while ($row = $stmt->fetch()) {
        echo "ID: " . $row['id'] . ", Name: " . $row['name'] . ", Description: " . $row['description'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>