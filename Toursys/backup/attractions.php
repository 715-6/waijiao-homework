<?php
include 'config.php';
include 'header.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$attraction_id = (int)$_GET['id'];

// 获取景点详情
try {
    $stmt = executeQuery($pdo, "SELECT * FROM attractions WHERE id = ?", [$attraction_id]);
    $attraction = $stmt->fetch();
    
    if (!$attraction) {
        echo "<div class='container'><p>景点不存在</p></div>";
        include 'footer.php';
        exit;
    }
} catch (PDOException $e) {
    error_log("获取景点详情失败: " . $e->getMessage());
    echo "<div class='container'><p>系统错误，请稍后再试</p></div>";
    include 'footer.php';
    exit;
}

// 获取评论
try {
    $stmt = executeQuery($pdo, "SELECT c.*, u.username, u.avatar FROM comments c JOIN users u ON c.user_id = u.id WHERE c.attraction_id = ? ORDER BY c.created_at DESC", [$attraction_id]);
    $comments = $stmt->fetchAll();
} catch (PDOException $e) {
    $comments = [];
    error_log("获取评论失败: " . $e->getMessage());
}
?>

    <div class="container">
        <div class="attraction-detail">
            <div class="detail-header">
                <img src="<?php echo htmlspecialchars($attraction['image_url']); ?>" alt="<?php echo htmlspecialchars($attraction['name']); ?>">
                <div class="detail-info">
                    <h1><?php echo htmlspecialchars($attraction['name']); ?></h1>
                    <p class="location">📍 <?php echo htmlspecialchars($attraction['location']); ?></p>
                    <div class="rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?php echo $i <= round($attraction['rating']) ? 'filled' : ''; ?>">★</span>
                        <?php endfor; ?>
                        <span>(<?php echo $attraction['rating']; ?>)</span>
                    </div>
                    <p class="price">门票: ¥<?php echo $attraction['price']; ?></p>
                    <p class="category">分类: <?php echo htmlspecialchars($attraction['category']); ?></p>
                </div>
            </div>

            <div class="description">
                <h2>景点介绍</h2>
                <p><?php echo nl2br(htmlspecialchars($attraction['description'])); ?></p>
            </div>

            <div class="comments-section">
                <h2>游客评价</h2>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="comment-form">
                        <h3>发表评论</h3>
                        <form action="add_comments.php" method="POST">
                            <input type="hidden" name="attraction_id" value="<?php echo $attraction_id; ?>">
                            <?php include 'csrf.php'; echo CSRFProtection::getTokenField(); ?>
                            <div class="rating-input">
                                <label>评分:</label>
                                <select name="rating">
                                    <option value="5">5星</option>
                                    <option value="4">4星</option>
                                    <option value="3">3星</option>
                                    <option value="2">2星</option>
                                    <option value="1">1星</option>
                                </select>
                            </div>
                            <textarea name="content" placeholder="写下您的评论..." required></textarea>
                            <button type="submit">提交评论</button>
                        </form>
                    </div>
                <?php else: ?>
                    <p><a href="login.php">登录</a>后即可发表评论</p>
                <?php endif; ?>

                <div class="comments-list">
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <div class="comment-header">
                                <img src="avatars/<?php echo htmlspecialchars($comment['avatar']); ?>" alt="<?php echo htmlspecialchars($comment['username']); ?>">
                                <div>
                                    <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                                    <div class="rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="star <?php echo $i <= $comment['rating'] ? 'filled' : ''; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="date"><?php echo date('Y-m-d', strtotime($comment['created_at'])); ?></span>
                                </div>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($comments)): ?>
                        <p>暂无评论，快来发表第一条评论吧！</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php include 'footer.php'; ?>