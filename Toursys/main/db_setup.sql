CREATE DATABASE toursys;
CREATE USER 'toursys_user'@'%' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON toursys.* TO 'toursys_user'@'%';
FLUSH PRIVILEGES;