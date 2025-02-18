CREATE DATABASE TaskManagementSystem;
USE TaskManagementSystem;


CREATE TABLE Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) NOT NULL UNIQUE,
    PasswordHash VARCHAR(255) NOT NULL,
    Role ENUM('Admin', 'Employee') DEFAULT 'Employee',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE Users ADD Email VARCHAR(255) NOT NULL UNIQUE;


CREATE TABLE Tasks (
    TaskID INT AUTO_INCREMENT PRIMARY KEY,
    Title VARCHAR(100) NOT NULL,
    Description TEXT,
    AssignedTo INT NOT NULL,
    AssignedBy INT NOT NULL,
    Status ENUM('Pending', 'In Progress', 'Completed') DEFAULT 'Pending',
    Deadline DATETIME,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (AssignedTo) REFERENCES Users(UserID),
    FOREIGN KEY (AssignedBy) REFERENCES Users(UserID)
);

CREATE TABLE Notifications (
    NotificationID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    Message TEXT NOT NULL,
    IsRead BOOLEAN DEFAULT FALSE,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);

ALTER TABLE notifications DROP FOREIGN KEY notifications_ibfk_1;

ALTER TABLE notifications
ADD CONSTRAINT notifications_ibfk_1
FOREIGN KEY (UserID) REFERENCES users(UserID)
ON DELETE CASCADE;

-- Table: Salaries
CREATE TABLE Salaries (
    SalaryID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    Amount DECIMAL(10, 2) NOT NULL,
    EffectiveDate DATE NOT NULL,
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);
SELECT Users.UserID, Users.Username, Salaries.SalaryID, Salaries.Amount 
FROM Users 
INNER JOIN Salaries ON Users.UserID = Salaries.UserID
ALTER TABLE salaries DROP FOREIGN KEY salaries_ibfk_1;
ALTER TABLE salaries ADD CONSTRAINT salaries_ibfk_1 
FOREIGN KEY (UserID) REFERENCES users(UserID) 
ON DELETE CASCADE;

--password reset table
CREATE TABLE PasswordResets (
    ResetID INT AUTO_INCREMENT PRIMARY KEY,
    Email VARCHAR(100) NOT NULL,
    Token VARCHAR(64) NOT NULL,
    Expires DATETIME NOT NULL
);

INSERT INTO Users (Username, PasswordHash, Role)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin');

INSERT INTO Users (Username, PasswordHash, Role)
VALUES ('employee1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Employee');

INSERT INTO Tasks (Title, Description, AssignedTo, AssignedBy, Deadline)
VALUES ('Fix Bug', 'Fix the login bug in the application', 2, 1, '2023-12-31 23:59:59');

INSERT INTO Notifications (UserID, Message)
VALUES (2, 'You have been assigned a new task: Fix Bug');