-- Create a new database if it doesn't exist
CREATE DATABASE IF NOT EXISTS 431Final_NguyenBrandon;

-- Switch to the newly created database
USE 431Final_NguyenBrandon;

-- Create a table for storing usernames and passwords
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);



