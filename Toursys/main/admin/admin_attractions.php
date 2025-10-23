<?php
require_once 'auth_check.php';
include 'header.php';

// 处理删除景区请求
if (isset($_GET['delete_attraction'])) {
    $attractionId = intval($_GET['delete_attraction']);
    try {
        $stmt = $pdo->prepare("DELETE FROM attractions WHERE id = ?");
        $stmt->execute([$attractionId]);
        $deleteMessage = "景区删除成功";
    } catch (PDOException $e) {
        error_log("删除景区时出错: " . $e->getMessage());
        $deleteMessage = "删除景区时出错";
    }
}

// 处理添加/编辑景区请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = cleanInput($_POST['name']);
    $description = cleanInput($_POST['description']);
    $location = cleanInput($_POST['location']);
    $category = cleanInput($_POST['category']);
    $price = floatval($_POST['price']);
    
    // 处理图片上传
    $image_url = null;
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // 编辑时获取原有图片URL
        $id = intval($_POST['id']);
        try {
            $stmt = $pdo->prepare("SELECT image_url FROM attractions WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch();
            $image_url = $existing['image_url'];
        } catch (PDOException $e) {
            error_log("获取景区信息时出错: " . $e->getMessage());
        }
    }
    
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../img/';
        $imageFileType = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
        
        // 检查文件是否为真实的图片
        $check = getimagesize($_FILES['cover_image']['tmp_name']);
        if ($check !== false) {
            // 检查文件大小 (最大5MB)
            if ($_FILES['cover_image']['size'] <= 5000000) {
                // 允许的图片格式
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($imageFileType, $allowedTypes)) {
                    // 生成唯一的文件名
                    $newFileName = 'attraction_' . time() . '_' . rand(1000, 9999) . '.' . $imageFileType;
                    $newFilePath = $uploadDir . $newFileName;
                    
                    // 检查目录是否存在且可写
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    if (is_writable($uploadDir)) {
                        // 移动上传的文件到指定目录
                        if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $newFilePath)) {
                            $image_url = 'img/' . $newFileName;
                            
                            // 如果是更新操作且原有图片存在，则删除原有图片
                            if (isset($_POST['id']) && !empty($_POST['id']) && !empty($existing['image_url'])) {
                                $oldImagePath = '../' . $existing['image_url'];
                                if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                                    unlink($oldImagePath);
                                }
                            }
                        } else {
                            error_log("文件上传失败: tmp_name=" . $_FILES['cover_image']['tmp_name'] . ", newFilePath=" . $newFilePath);
                        }
                    }
                }
            }
        }
    }
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // 更新景区
        $id = intval($_POST['id']);
        try {
            $stmt = $pdo->prepare("UPDATE attractions SET name = ?, description = ?, location = ?, category = ?, price = ?, image_url = ? WHERE id = ?");
            $stmt->execute([$name, $description, $location, $category, $price, $image_url, $id]);
            $message = "景区更新成功";
        } catch (PDOException $e) {
            error_log("更新景区时出错: " . $e->getMessage());
            $message = "更新景区时出错";
        }
    } else {
        // 添加新景区
        try {
            $stmt = $pdo->prepare("INSERT INTO attractions (name, description, location, category, price, image_url) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $location, $category, $price, $image_url]);
            $message = "新景区添加成功";
        } catch (PDOException $e) {
            error_log("添加景区时出错: " . $e->getMessage());
            $message = "添加景区时出错";
        }
    }
}

// 获取所有景区
$attractions = [];
try {
    $stmt = $pdo->query("SELECT id, name, description, location, category, price, image_url, created_at FROM attractions ORDER BY created_at DESC");
    $attractions = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("获取景区列表时出错: " . $e->getMessage());
    $errorMessage = "获取景区列表时出错";
}

// 获取单个景区信息（用于编辑）
$editAttraction = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM attractions WHERE id = ?");
        $stmt->execute([$editId]);
        $editAttraction = $stmt->fetch();
    } catch (PDOException $e) {
        error_log("获取景区信息时出错: " . $e->getMessage());
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">景区管理</h1>
</div>

<?php if (isset($message)): ?>
<div class="alert alert-info"><?php echo $message; ?></div>
<?php endif; ?>

<?php if (isset($deleteMessage)): ?>
<div class="alert alert-info"><?php echo $deleteMessage; ?></div>
<?php endif; ?>

<?php if (isset($errorMessage)): ?>
<div class="alert alert-danger"><?php echo $errorMessage; ?></div>
<?php endif; ?>

<!-- 添加/编辑景区表单 -->
<div class="card mb-4">
    <div class="card-header">
        <h5><?php echo $editAttraction ? '编辑景区' : '添加新景区'; ?></h5>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <?php if ($editAttraction): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($editAttraction['id']); ?>">
            <?php endif; ?>
            
            <div class="mb-3">
                <label for="name" class="form-label">名称</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?php echo $editAttraction ? htmlspecialchars($editAttraction['name']) : ''; ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">描述</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo $editAttraction ? htmlspecialchars($editAttraction['description']) : ''; ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="location" class="form-label">位置</label>
                    <input type="text" class="form-control" id="location" name="location" 
                           value="<?php echo $editAttraction ? htmlspecialchars($editAttraction['location']) : ''; ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="category" class="form-label">类别</label>
                    <input type="text" class="form-control" id="category" name="category" 
                           value="<?php echo $editAttraction ? htmlspecialchars($editAttraction['category']) : ''; ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="price" class="form-label">价格</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" 
                       value="<?php echo $editAttraction ? htmlspecialchars($editAttraction['price']) : ''; ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="cover_image" class="form-label">封面图片</label>
                <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
                <?php if ($editAttraction && !empty($editAttraction['image_url'])): ?>
                    <div class="mt-2">
                        <small class="text-muted">当前图片:</small>
                        <img src="../<?php echo htmlspecialchars($editAttraction['image_url']); ?>" alt="当前封面" style="max-height: 100px;">
                    </div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <?php echo $editAttraction ? '更新景区' : '添加景区'; ?>
            </button>
            
            <?php if ($editAttraction): ?>
                <a href="admin_attractions.php" class="btn btn-secondary">取消</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- 景区列表 -->
<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>名称</th>
                <th>位置</th>
                <th>类别</th>
                <th>价格</th>
                <th>封面图片</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($attractions as $attraction): ?>
            <tr>
                <td><?php echo htmlspecialchars($attraction['id']); ?></td>
                <td><?php echo htmlspecialchars($attraction['name']); ?></td>
                <td><?php echo htmlspecialchars($attraction['location']); ?></td>
                <td><?php echo htmlspecialchars($attraction['category']); ?></td>
                <td>¥<?php echo number_format($attraction['price'], 2); ?></td>
                <td>
                    <?php if (!empty($attraction['image_url'])): ?>
                        <img src="../<?php echo htmlspecialchars($attraction['image_url']); ?>" alt="封面" style="max-height: 50px;">
                    <?php else: ?>
                        无图片
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($attraction['created_at']); ?></td>
                <td>
                    <a href="?edit=<?php echo $attraction['id']; ?>" class="btn btn-primary btn-sm">编辑</a>
                    <a href="?delete_attraction=<?php echo $attraction['id']; ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('确定要删除景区 <?php echo htmlspecialchars($attraction['name']); ?> 吗？')">
                        删除
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>