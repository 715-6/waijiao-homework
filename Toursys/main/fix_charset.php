<?php
require_once 'config.php';

try {
    echo "Connected successfully\n";
    
    // 清空attractions表
    $pdo->exec("DELETE FROM attractions");
    echo "Cleared attractions table\n";
    
    // 使用正确的字符集插入数据
    $pdo->exec("SET NAMES utf8mb4");
    
    $sql = "INSERT INTO attractions (name, description, location, image_url, category, price) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    $attractions = [
        ['长城', '长城是中国古代的军事防御工程，是世界文化遗产。', '北京', 'images/great_wall.jpg', '历史遗迹', 45.00],
        ['故宫', '故宫是中国明清两代的皇家宫殿，位于北京中轴线的中心。', '北京', 'images/forbidden_city.jpg', '历史遗迹', 60.00],
        ['西湖', '西湖位于杭州市，是中国著名的风景名胜区。', '杭州', 'images/west_lake.jpg', '自然风光', 0.00],
        ['黄山', '黄山位于安徽省，以奇松、怪石、云海、温泉四绝著称。', '黄山', 'images/huangshan.jpg', '自然风光', 230.00]
    ];
    
    foreach ($attractions as $attr) {
        $result = $stmt->execute($attr);
        if ($result) {
            echo "Inserted: " . $attr[0] . "\n";
        } else {
            echo "Failed to insert: " . $attr[0] . "\n";
        }
    }
    
    // 验证插入的数据
    echo "\nVerifying data:\n";
    $stmt = $pdo->query("SELECT id, name, description FROM attractions;");
    while ($row = $stmt->fetch()) {
        echo "ID: " . $row['id'] . ", Name: " . $row['name'] . ", Description: " . $row['description'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>