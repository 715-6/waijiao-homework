-- 创建数据库
CREATE DATABASE travel_site;
USE travel_site;

-- 用户表
CREATE TABLE users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       username VARCHAR(50) UNIQUE NOT NULL,
                       email VARCHAR(100) UNIQUE NOT NULL,
                       password VARCHAR(255) NOT NULL,
                       avatar VARCHAR(255) DEFAULT 'default.jpg',
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 景点表
CREATE TABLE attractions (
                             id INT AUTO_INCREMENT PRIMARY KEY,
                             name VARCHAR(100) NOT NULL,
                             description TEXT,
                             location VARCHAR(100),
                             image_url VARCHAR(255),
                             category VARCHAR(50),
                             price DECIMAL(10,2),
                             rating DECIMAL(3,2) DEFAULT 0,
                             created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 评论表
CREATE TABLE comments (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          user_id INT,
                          attraction_id INT,
                          content TEXT,
                          rating INT,
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          FOREIGN KEY (user_id) REFERENCES users(id),
                          FOREIGN KEY (attraction_id) REFERENCES attractions(id)
);

-- 插入示例数据
INSERT INTO attractions (name, description, location, image_url, category, price) VALUES
                                                                                      ('长城', '长城是中国古代的军事防御工程，是世界文化遗产。', '北京', 'images/great_wall.jpg', '历史遗迹', 45.00),
                                                                                      ('故宫', '故宫是中国明清两代的皇家宫殿，位于北京中轴线的中心。', '北京', 'images/forbidden_city.jpg', '历史遗迹', 60.00),
                                                                                      ('西湖', '西湖位于杭州市，是中国著名的风景名胜区。', '杭州', 'images/west_lake.jpg', '自然风光', 0.00),
                                                                                      ('黄山', '黄山位于安徽省，以奇松、怪石、云海、温泉四绝著称。', '黄山', 'images/huangshan.jpg', '自然风光', 230.00);