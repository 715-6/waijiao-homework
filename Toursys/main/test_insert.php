<?php
require_once 'config.php';

try {
    echo "Connected successfully\n";
    
    // 检查表是否存在
    $stmt = $pdo->query("SHOW TABLES");
    echo "Tables in database:\n";
    while ($row = $stmt->fetch()) {
        echo $row['Tables_in_toursys'] . "\n";
    }
    
    // 检查attractions表中的记录数
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM attractions");
    $result = $stmt->fetch();
    echo "Number of attractions before insert: " . $result['count'] . "\n";
    
    // 尝试插入一条测试记录
    $sql = "INSERT INTO attractions (name, description, location, image_url, category, price) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        '测试景点',
        '这是一个测试景点',
        '测试地点',
        'images/test.jpg',
        '自然风光',
        100.00
    ]);
    
    if ($result) {
        echo "Test record inserted successfully\n";
    } else {
        echo "Failed to insert test record\n";
    }
    
    // 再次检查attractions表中的记录数
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM attractions");
    $result = $stmt->fetch();
    echo "Number of attractions after insert: " . $result['count'] . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>