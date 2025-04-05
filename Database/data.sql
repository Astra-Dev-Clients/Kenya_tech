create database if not exists Kenya_Tech;
use Kenya_Tech;


CREATE TABLE `users` (
  `SN` int primary key auto_increment,
  `First_Name` varchar(50),
  `Last_Name` varchar(50),
  `Phone` varchar(20) unique,
  `Email` varchar(100) unique,
  `Avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'images/pic-1.jpg',
  `User_Role` varchar(100) DEFAULT 'user',
  `Pass` varchar(255),
  `Reg_Date` datetime default now()
);


CREATE TABLE user_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


