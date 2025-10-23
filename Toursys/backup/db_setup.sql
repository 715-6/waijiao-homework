CREATE DATABASE toursys;
CREATE USER 'toursys_user'@'localhost' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON toursys.* TO 'toursys_user'@'localhost';
FLUSH PRIVILEGES;