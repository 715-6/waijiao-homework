<?php
require_once 'auth_check.php';
include 'header.php';

// 获取统计信息
$userCount = 0;
$attractionCount = 0;

try {
    // 用户总数
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    $userCount = $result['count'];
    
    // 景区总数
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM attractions");
    $result = $stmt->fetch();
    $attractionCount = $result['count'];
} catch (PDOException $e) {
    error_log("获取统计信息时出错: " . $e->getMessage());
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">仪表板</h1>
</div>

<div class="row">
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <h5 class="card-title">用户总数</h5>
                <p class="card-text display-4"><?php echo $userCount; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <h5 class="card-title">景区总数</h5>
                <p class="card-text display-4"><?php echo $attractionCount; ?></p>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>