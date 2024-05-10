<?php
// Project name: CPSC 431 Final Project - ToDo List 
// File name:  list.php
// Date : April 24 2024
// Author: Diego Barrales

// Uncomment below if you need to have detailed error reporting
error_reporting(E_ALL);

// This file requires the the variable $_SESSION['ID'] to be set
// The only place $_SESSION['ID'] is set is in index.php on a successful login
session_start();

// Include the Handler class so we can use it. 
require("helpers/handler.php");

// Create a new request handler. 
$handler = new Handler();

// Process the request (Will execute one of the GET/PUT/POST/DELETE functions below)
$handler->process();

// This function executes if you create a fetch() request to api/list.php and use "GET" as the method
// Expected URL parameters: optional `list_id`
// Output: Titles of user lists or Items of one list or empty array
function GET(Handler $handler)
{

    $pdo = $handler->db->PDO();

    // Grab expected parameters from URL
    // $user_id = $handler->request->get['user_id'] ?? '';
    $list_id = $handler->request->get['list_id'] ?? '';

    $list = [];

    if (empty($list_id)) {
        // $list = getLists($pdo, $user_id);
        $list = getLists($pdo);
    } else {
        // $list = getList($pdo, $user_id, $list_id);
        $list = getList($pdo, $list_id);
    }
    $handler->response->json($list);
}

// This function executes if you create a fetch() request to api/list.php and use "DELETE" as the method
// Delete a single list connected to that user or Delete single item from a list
// Expected URL parameters: ID of list, `list_id` and optional ID of item, `item_id`
// Output: ok or error
function DELETE(Handler $handler)
{
    $list_id = $handler->request->get['list_id'] ?? '';
    $item_id = $handler->request->get['item_id'] ?? '';

    if (empty($list_id)) {
        $handler->response->json(['error' => 'list_id needs to be provided']);
    }

    $pdo = $handler->db->PDO();

    if (!empty($item_id)) {
        deleteItem($pdo, $list_id, $item_id);
    } else {
        deleteList($pdo, $list_id);
    }

    $handler->response->json(['ok' => 'successfully deleted!']);
}

// This function executes if you create a fetch() request to api/list.php and use "POST" as the method
// Used to create a list
// Expected JSON: Name of the list := `name`
// Output: ok or error
function POST(Handler $handler)
{
    $list_name = $handler->request->post['name'] ?? '';

    if (empty($list_name)) {
        $handler->response->json(['error' => 'Need to name the new list']);
    }

    $pdo = $handler->db->PDO();

    $query = "INSERT INTO `lists`(`user_id`, `name`) VALUES (:uid, :list_name);";

    $statement = $pdo->prepare($query);

    $parameters = [":uid" => $_SESSION['ID'], ":list_name" => $list_name];

    $statement->execute($parameters);
    $handler->response->json(['ok' => $list_name . ' created successfully']);
}

// This function executes if you create a fetch() request to api/list.php and use "PUT" as the method
// Used to update list items
// Expected JSON:
//  `item`: {`id`, optional `text`, `checked` }
//  `list_id`: ''
// Output: item or error
function PUT(Handler $handler)
{
    $item = $handler->request->post['item'] ?? null;

    if (!isset($item)) {
        $handler->response->json(['error' => 'item was not provided']);
    }

    // expect list_id seperately
    $list_id = $handler->request->post['list_id'] ?? '';

    $db_item = [];
    $pdo = $handler->db->PDO();

    if (isset($item['text'])) {
        $ret_item = addItem($pdo, $list_id, $item['text']);
    } else {
        $ret_item = updateItem($pdo, $list_id, $item);
    }

    $handler->response->json(['item' => $ret_item]);
}

// Returns array of LISTS, each with the following structure: { id, name, created }
function getLists(PDO $pdo)
{
    $query = "CALL getLists(?);";

    $statement = $pdo->prepare($query);

    $statement->execute([$_SESSION['ID']]);

    // No error checking needed because if it returns empty then that means that user just does not have any lists
    return $statement->fetchAll();
}

// Returns array of ITEMS, each with the following structure: { id, text, checked, created }
// Returns array of ITEMS, each with the following structure: { text }
function getList(PDO $pdo, string $list_id)
{
    $query = "CALL getList(:uid, :lid);";

    $statement = $pdo->prepare($query);

    $parameters = [":uid" => $_SESSION['ID'], ":lid" => $list_id];

    $statement->execute($parameters);

    // Fetch only the `text` property from each row
    $taskItems = [];
    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $taskItems[] = ["text" => $row["text"]];
    }

    return $taskItems;
}


// check / uncheck item to that list
function updateItem(PDO $pdo, $list_id, $item)
{
    $query = "UPDATE `list_items` SET `checked`=:checked WHERE `list_id`=:lid AND `item_id`=:iid;";

    $statement = $pdo->prepare($query);

    $parameters = [
        ":checked" => $item['checked'],
        ":lid" => $list_id,
        ":iid" => $item['id']
    ];

    $statement->execute($parameters);
}

function addItem(PDO $pdo, $list_id, $text)
{
    $query = "INSERT INTO `list_items`(`list_id`, `text`) VALUES (:lid, :text);";

    $statement = $pdo->prepare($query);

    $parameters = [":lid" => $list_id, ":text" => $text];

    $statement->execute($parameters);
}

function deleteItem(PDO $pdo, $list_id, $item_id)
{
    $query = "DELETE FROM `list_items` WHERE `list_id`=:lid AND `item_id`=:iid;";

    $statement = $pdo->prepare($query);

    $parameters = [":lid" => $list_id, ":iid" => $item_id];

    $statement->execute($parameters);

    $deletedRows = $statement->rowCount();
    return $deletedRows > 0; 
}

function deleteList(PDO $pdo, $list_id)
{
    $query = "DELETE FROM `lists` WHERE `user_id`=:uid AND `list_id`=:lid;";

    $parameters = [":uid" => $_SESSION['ID'], ":lid" => $list_id];

    $statement = $pdo->prepare($query);

    $statement->execute($parameters);
}
