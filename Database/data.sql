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


CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Event_Date DATETIME NOT NULL,
    event_id VARCHAR(100) UNIQUE NOT NULL,
    organizer_id INT NOT NULL,
    poster VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    General_Admission DECIMAL (10,2),
    VIP DECIMAL (10,2),
    Early_Bird DECIMAL (10,2),
    General_Admission_previledges text NOT NULL,
    VIP_previledges text NOT NULL,
    Early_Bird_previledges text NOT NULL,
    status ENUM('upcoming', 'ongoing', 'completed') DEFAULT 'upcoming',
    mode ENUM('physical', 'online'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES users(SN)
);





CREATE TABLE event_attendees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    ticket_type ENUM('General Admission', 'VIP', 'Early Bird') NOT NULL,
    ticket_price DECIMAL(10,2) NOT NULL,
    purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id),
    FOREIGN KEY (user_id) REFERENCES users(SN)
);


CREATE TABLE event_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id),
    FOREIGN KEY (user_id) REFERENCES users(SN)
);


CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    ticket_type ENUM('General Admission', 'VIP', 'Early Bird') NOT NULL,
    ticket_price DECIMAL(10,2) NOT NULL,
    purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id),
    FOREIGN KEY (user_id) REFERENCES users(SN)
);






