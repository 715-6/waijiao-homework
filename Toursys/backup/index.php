<?php
include 'config.php';
include 'header.php';

// 获取景点列表
try {
    $stmt = executeQuery($pdo, "SELECT * FROM attractions ORDER BY rating DESC LIMIT 6");
    $attractions = $stmt->fetchAll();
} catch (PDOException $e) {
    $attractions = [];
    error_log("获取景点列表失败: " . $e->getMessage());
}
?>

    <div class="container">
        <div class="hero-section">
            <h1>探索世界之美</h1>
            <p>发现全球最令人惊叹的旅游目的地</p>
            <form action="search.php" method="GET" class="search-form">
                <input type="text" name="query" placeholder="搜索景点、城市或国家...">
                <button type="submit">搜索</button>
            </form>
        </div>

        <h2>热门景点</h2>
        <div class="attractions-grid">
            <?php foreach ($attractions as $attraction): ?>
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
    </div>

<?php include 'footer.php'; ?>