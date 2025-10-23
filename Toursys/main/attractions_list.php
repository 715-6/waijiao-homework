<?php
include 'config.php';
include 'header.php';

// 获取所有景点
try {
    $stmt = executeQuery($pdo, "SELECT * FROM attractions ORDER BY rating DESC");
    $attractions = $stmt->fetchAll();
} catch (PDOException $e) {
    $attractions = [];
    error_log("获取景点列表失败: " . $e->getMessage());
}
?>

    <div class="container">
        <h2>景点大全</h2>
        <div class="attractions-grid">
            <?php if (empty($attractions)): ?>
                <p>暂无景点信息</p>
            <?php else: ?>
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
            <?php endif; ?>
        </div>
    </div>

<?php include 'footer.php'; ?>