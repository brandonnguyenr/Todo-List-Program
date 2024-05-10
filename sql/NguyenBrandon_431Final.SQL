-- Create a new database if it doesn't exist
CREATE DATABASE IF NOT EXISTS 431Final_NguyenBrandon;

-- Switch to the newly created database
USE 431Final_NguyenBrandon;

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE IF NOT EXISTS `getList` (IN `_user_id` INT, IN `_list_id` INT)   WITH user_lists AS (SELECT lists.list_id FROM `lists`WHERE lists.user_id=_user_id)
	SELECT list_items.item_id as id, list_items.text, list_items.checked, list_items.created  
	FROM user_lists 
    JOIN list_items 
    ON user_lists.list_id=list_items.list_id 
    	AND list_items.list_id=_list_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE IF NOT EXISTS `getLists` (IN `_user_id` INT)   SELECT `list_id` as id, `name`, `created` FROM `lists` WHERE `user_id`=_user_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE IF NOT EXISTS `getUser` (IN `_username` VARCHAR(50))   SELECT * FROM users WHERE username=_username$$

CREATE DEFINER=`root`@`localhost` PROCEDURE IF NOT EXISTS `postUser` (IN `_username` VARCHAR(50), IN `_password` VARCHAR(255))  MODIFIES SQL DATA BEGIN
INSERT INTO users (username, password) VALUES (_username, _password);
CALL `getUser`(_username);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE IF NOT EXISTS `deleteItem`(IN `_list_id` INT(11), IN `_item_id` INT(11))
    MODIFIES SQL DATA
DELETE FROM `list_items` WHERE `list_id`=_list_id AND `item_id`=_item_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE IF NOT EXISTS `deleteList`(IN `_user_id` INT(11), IN `_list_id` INT(11))
    MODIFIES SQL DATA
DELETE FROM `lists` WHERE `user_id`=_user_id AND `list_id`=_list_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE IF NOT EXISTS `postItem`(IN `_list_id` INT(11), IN `_text` VARCHAR(255))
BEGIN
INSERT INTO `list_items`(`list_id`, `text`) VALUES (_list_id, _text);
SELECT * FROM `list_items` WHERE list_items.list_id=_list_id ORDER BY list_items.item_id DESC LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE IF NOT EXISTS `postList`(IN `_user_id` INT(11), IN `_name` VARCHAR(255))
BEGIN
INSERT INTO `lists`(`user_id`, `name`) VALUES (_user_id, _name);
SELECT * FROM `lists` WHERE lists.user_id=_user_id ORDER BY lists.list_id DESC LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE IF NOT EXISTS `updateItem`(IN `_list_id` INT(11), IN `_item_id` INT(11), IN `_checked` TINYINT(1))
BEGIN
UPDATE `list_items` SET `checked`=_checked WHERE `list_id`=_list_id AND `item_id`=_item_id;
SELECT * FROM `list_items` WHERE list_items.list_id=_list_id AND list_items.item_id=_item_id;
END$$

DELIMITER ;

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
    created DATETIME NOT NULL DEFAULT current_timestamp(),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Create a table for storing list items
CREATE TABLE IF NOT EXISTS list_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    list_id INT NOT NULL,
    text VARCHAR(255) NOT NULL,
    checked BOOLEAN NOT NULL DEFAULT 0,
    created DATETIME NOT NULL DEFAULT current_timestamp(),
    FOREIGN KEY (list_id) REFERENCES lists(list_id) ON DELETE CASCADE
);




