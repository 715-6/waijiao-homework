<?php
include 'config.php';
include 'header.php';

// 获取搜索关键词
$query = isset($_GET['query']) ? cleanInput($_GET['query']) : '';

// 如果有搜索关键词，则执行搜索
if (!empty($query)) {
    try {
        // 使用LIKE操作符进行模糊搜索
        $stmt = executeQuery($pdo, "SELECT * FROM attractions WHERE name LIKE ? OR location LIKE ? OR description LIKE ?", 
                             ["%$query%", "%$query%", "%$query%"]);
        $results = $stmt->fetchAll();
    } catch (PDOException $e) {
        $results = [];
        error_log("搜索景点失败: " . $e->getMessage());
    }
} else {
    $results = [];
}
?>

    <div class="container">
        <h1>搜索结果</h1>
        
        <form action="search.php" method="GET" class="search-form">
            <input type="text" name="query" placeholder="搜索景点、城市或国家..." value="<?php echo htmlspecialchars($query); ?>">
            <button type="submit">搜索</button>
        </form>
        
        <?php if (!empty($query)): ?>
            <h2> "<?php echo htmlspecialchars($query); ?>" 的搜索结果</h2>
            
            <?php if (count($results) > 0): ?>
                <div class="attractions-grid">
                    <?php foreach ($results as $attraction): ?>
                        <div class="attraction-card">
                            <img src="<?php echo htmlspecialchars($attraction['image_url']); ?>" alt="<?php echo htmlspecialchars($attraction['name']); ?>">
                            <div class="card-content">
                                <h3><?php echo htmlspecialchars($attraction['name']); ?></h3>
                                <p><?php echo htmlspecialchars($attraction['location']); ?></p>
                                <div class="rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="star <?php echo $i <= round($attraction['rating']) ? 'filled' : ''; ?>">★</span>
                                    <?php endfor; ?>
                                    <span>(<?php echo $attraction['rating']; ?>)</span>
                                </div>
                                <p class="price">¥<?php echo $attraction['price']; ?></p>
                                <a href="attractions.php?id=<?php echo $attraction['id']; ?>" class="btn">查看详情</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>未找到与 "<?php echo htmlspecialchars($query); ?>" 相关的景点。</p>
            <?php endif; ?>
        <?php else: ?>
            <p>请输入关键词进行搜索。</p>
        <?php endif; ?>
    </div>

<?php include 'footer.php'; ?>