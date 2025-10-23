<?php
require_once 'auth_check.php';
include 'header.php';

// 处理删除用户请求
if (isset($_GET['delete_user'])) {
    $userId = intval($_GET['delete_user']);
    
    // 检查是否是最后一个管理员账户
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as adminCount FROM users WHERE is_admin = 1 AND id != ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        
        if ($result['adminCount'] == 0) {
            $deleteMessage = "无法删除最后一个管理员账户！系统必须至少保留一个管理员账户。";
        } else {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $deleteMessage = "用户删除成功";
        }
    } catch (PDOException $e) {
        error_log("删除用户时出错: " . $e->getMessage());
        $deleteMessage = "删除用户时出错";
    }
}

// 获取所有用户和管理员计数
$users = [];
$adminCount = 0;
try {
    $stmt = $pdo->query("SELECT id, username, email, created_at, is_admin FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
    
    $stmt = $pdo->query("SELECT COUNT(*) as adminCount FROM users WHERE is_admin = 1");
    $result = $stmt->fetch();
    $adminCount = $result['adminCount'];
} catch (PDOException $e) {
    error_log("获取用户列表时出错: " . $e->getMessage());
    $errorMessage = "获取用户列表时出错";
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">用户管理</h1>
</div>

<?php if (isset($deleteMessage)): ?>
<div class="alert alert-info"><?php echo $deleteMessage; ?></div>
<?php endif; ?>

<?php if (isset($errorMessage)): ?>
<div class="alert alert-danger"><?php echo $errorMessage; ?></div>
<?php endif; ?>

<div class="alert alert-warning">
    <strong>注意：</strong>系统中必须至少保留一个管理员账户。当前共有 <?php echo $adminCount; ?> 个管理员账户。
    删除所有管理员账户将导致无法访问管理员面板。
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>用户名</th>
                <th>邮箱</th>
                <th>注册时间</th>
                <th>角色</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                <td>
                    <?php if ($user['is_admin']): ?>
                        <span class="badge bg-primary">管理员</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">普通用户</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($user['is_admin'] && $adminCount <= 1): ?>
                        <button class="btn btn-danger btn-sm" disabled>删除</button>
                        <small class="text-muted">最后一个管理员</small>
                    <?php else: ?>
                        <a href="?delete_user=<?php echo $user['id']; ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('确定要删除用户 <?php echo htmlspecialchars($user['username']); ?> 吗？')">
                            删除
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>