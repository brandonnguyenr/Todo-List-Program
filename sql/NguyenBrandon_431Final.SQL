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

-- Create a table for storing lists
CREATE TABLE IF NOT EXISTS lists (
    list_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    created DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Create a table for storing list items
CREATE TABLE IF NOT EXISTS list_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    list_id INT NOT NULL,
    text VARCHAR(255) NOT NULL,
    checked BOOLEAN NOT NULL,
    created DATETIME NOT NULL,
    FOREIGN KEY (list_id) REFERENCES lists(list_id)
);




