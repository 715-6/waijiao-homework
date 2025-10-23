<?php
include 'config.php';
include 'header.php';
//获取景点列表
try {
    $stmt = executeQuery($pdo, "SELECT * FROM attractions ORDER BY rating DESC LIMIT 6");
    $attractions = $stmt->fetchAll();
} catch (PDOException $e) {
    $attractions = [];
    error_log("获取景点列表失败: " . $e->getMessage());
}
?>

<style>
    .hero-section {
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        min-height: 500px;
    }
    
    .hero-content {
        background-color: rgba(0, 0, 0, 0.3);
        padding: 30px;
        border-radius: 10px;
        max-width: 800px;
        width: 100%;
    }
    
    .hero-section h1 {
        font-size: 3rem;
        margin-bottom: 20px;
        color: white;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }
    
    .hero-section p {
        font-size: 1.2rem;
        margin-bottom: 30px;
        color: white;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }
</style>

    <div class="container">
        <div class="hero-section" style="background-image: url('img/chaka_lake_bg.jpg');">
            <div class="hero-content">
                <h1>天空之境 - 查卡盐湖</h1>
                <p>探索世界最美镜面湖，体验如梦如幻的天空之境</p>
                <form action="search.php" method="GET" class="search-form">
                    <input type="text" name="query" placeholder="搜索景点、城市或国家...">
                    <button type="submit">搜索</button>
                </form>
            </div>
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
