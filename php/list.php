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
    //$query = "CALL postList(:uid, :list_name);";

    $statement = $pdo->prepare($query);

    $parameters = [":uid" => $_SESSION['ID'], ":list_name" => $list_name];

    $statement->execute($parameters);
    $created_list = $statement->fetchAll()[0];

    $handler->response->json(['ok' => $list_name . ' created successfully', 'list' => $created_list]);
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


    // expect list_id separately
    $list_id = $handler->request->post['list_id'] ?? '';

    $pdo = $handler->db->PDO();

    if (!isset($item)) {
        $new_name = $handler->request->post['list_name'] ?? '';

        updateList($pdo, $list_id, $new_name);

        $handler->response->json(['ok' => 'list updated sucessfully']);
    }
    // if item was not provided then it is not an error, you just want to update the list name
    // $handler->response->json(['error' => 'item was not provided']);
    $ret_item = [];

    if (isset($item['text'])) {
        $ret_item = addItem($pdo, $list_id, $item['text']);
    } else {
        $ret_item = updateItem($pdo, $list_id, $item);
    }
    // Return the updated or added item
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
    // $query = "UPDATE `list_items` SET `checked`=:checked WHERE `list_id`=:lid AND `item_id`=:iid;";
    $query = "CALL updateItem(:lid, :iid, :checked);";

    $statement = $pdo->prepare($query);

    $parameters = [
        ":lid" => $list_id,
        ":iid" => $item['id'],
        ":checked" => $item['checked']
    ];

    $statement->execute($parameters);

    $updated_item = $statement->fetchAll()[0];
    return $updated_item;
}

function addItem(PDO $pdo, $list_id, $text)
{
    // $query = "INSERT INTO `list_items`(`list_id`, `text`) VALUES (:lid, :text);";
    $query = "CALL postItem(:lid, :text);";

    $statement = $pdo->prepare($query);

    $parameters = [":lid" => $list_id, ":text" => $text];

    $statement->execute($parameters);

    $created_item = $statement->fetchAll()[0];
    return $created_item;
}

function deleteItem(PDO $pdo, $list_id, $item_id)
{
    $query = "CALL deleteItem(:lid, :iid);";

    $statement = $pdo->prepare($query);

    $parameters = [":lid" => $list_id, ":iid" => $item_id];

    $statement->execute($parameters);
}

function deleteList(PDO $pdo, $list_id)
{
    $query = "CALL deleteList(:uid, :lid);";

    $parameters = [":uid" => $_SESSION['ID'], ":lid" => $list_id];

    $statement = $pdo->prepare($query);

    $statement->execute($parameters);
}

function updateList(PDO $pdo, $list_id, $new_name)
{
    $query = "UPDATE `lists` SET `name`=:name WHERE lists.list_id=:lid;";

    $parameters = [":name" => $new_name, ":lid" => $list_id];

    $statement = $pdo->prepare($query);

    $statement->execute($parameters);
}
