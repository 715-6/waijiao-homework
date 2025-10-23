<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            padding-top: 60px;
            color: white;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .nav-link {
            color: rgba(255,255,255,0.75);
        }
        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: #495057;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar col-md-3 col-lg-2 d-md-block">
        <div class="position-sticky pt-3">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="admin.php">
                        <i class="fas fa-home me-2"></i>仪表板
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_users.php">
                        <i class="fas fa-users me-2"></i>用户管理
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_attractions.php">
                        <i class="fas fa-mountain me-2"></i>景区管理
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index.php">
                        <i class="fas fa-globe me-2"></i>返回网站
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>退出登录
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">