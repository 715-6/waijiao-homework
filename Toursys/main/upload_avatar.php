<?php
// 检查会话是否已经启动
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 检查是否有文件上传
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    // 检查上传错误
    if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        switch ($_FILES['avatar']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $error = "文件大小超过服务器限制。";
                break;
            case UPLOAD_ERR_PARTIAL:
                $error = "文件只上传了一部分。";
                break;
            case UPLOAD_ERR_NO_FILE:
                $error = "没有选择文件。";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $error = "服务器临时文件夹缺失。";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $error = "无法写入文件到磁盘。";
                break;
            case UPLOAD_ERR_EXTENSION:
                $error = "文件上传被PHP扩展阻止。";
                break;
            default:
                $error = "文件上传失败，请稍后再试。";
                break;
        }
    } else {
        $uploadDir = 'avatars/';
        $uploadFile = $uploadDir . basename($_FILES['avatar']['name']);
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
        
        // 检查文件是否为真实的图片
        $check = getimagesize($_FILES['avatar']['tmp_name']);
        if ($check === false) {
            $error = "文件不是有效的图片。";
        } else {
            // 检查文件大小 (最大2MB)
            if ($_FILES['avatar']['size'] > 2000000) {
                $error = "文件大小不能超过2MB。";
            } else {
                // 允许的图片格式
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                if (!in_array($imageFileType, $allowedTypes)) {
                    $error = "只允许上传 JPG, JPEG, PNG, GIF 格式的图片。";
                } else {
                    // 生成唯一的文件名
                    $newFileName = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $imageFileType;
                    $newFilePath = $uploadDir . $newFileName;
                    
                    // 检查目录是否存在且可写
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    if (!is_writable($uploadDir)) {
                        $error = "上传目录不可写。";
                    } else {
                        // 移动上传的文件到指定目录
                        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $newFilePath)) {
                            try {
                                // 更新数据库中的头像路径
                                $stmt = executeQuery($pdo, "UPDATE users SET avatar = ? WHERE id = ?", 
                                                    [$newFileName, $_SESSION['user_id']]);
                                
                                // 更新成功
                                $success = "头像上传成功！";
                                
                                // 更新会话中的用户信息
                                $_SESSION['avatar'] = $newFileName;
                            } catch (PDOException $e) {
                                error_log("更新头像失败: " . $e->getMessage());
                                $error = "头像更新失败，请稍后再试！";
                                
                                // 删除上传的文件
                                if (file_exists($newFilePath)) {
                                    unlink($newFilePath);
                                }
                            }
                        } else {
                            // 获取更多错误信息
                            $error = "文件上传失败，请稍后再试。";
                            error_log("文件上传失败: tmp_name=" . $_FILES['avatar']['tmp_name'] . ", newFilePath=" . $newFilePath);
                        }
                    }
                }
            }
        }
    }
} else {
    $error = "没有接收到上传的文件。";
}

// 重定向回个人中心页面
if (isset($error)) {
    header("Location: profile.php?error=" . urlencode($error));
} else {
    header("Location: profile.php?success=" . urlencode($success));
}
exit;
?>