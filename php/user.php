<?php
// Project name: CPSC 431 Final Project - ToDo List 
// File name: user.php
// Date : May 24 2024
// Author: Diego Barrales

// Uncomment below if you need to have detailed error reporting
error_reporting(E_ALL);

// Include the Handler class so we can use it. 
require("helpers/handler.php");
require_once("helpers/error_codes.php");

// Create a new request handler. 
$handler = new Handler();

// Process the request (Will execute one of the GET/PUT/POST/DELETE functions below)
$handler->process();

// This function executes if you create a fetch() request to api/user.php and use "GET" as the method
// function GET(Handler $handler)
// {
// }

// This function executes if you create a fetch() request to api/user.php and use "DELETE" as the method
function DELETE(Handler $handler)
{
}

// This function executes if you create a fetch() request to api/user.php and use "POST" as the method
// Used to create a list
function POST(Handler $handler)
{
    $pdo = $handler->db->PDO();

    $user_id = null;
    $create = $handler->request->post['create'] ?? false;
    $username = $handler->request->post['username'] ?? '';
    $password = $handler->request->post['password'] ?? '';

    try {
        if ($create != false) {
            $user_id = postUser($pdo, $username, $password);
        } else {
            $user_id = getUser($pdo, $username, $password);
        }
    } catch (UserException $error) {
        $handler->response->json(['error' => $error->getMessage()]);
    }

    $handler->response->json(['user_id' => $user_id]);
}

// This function executes if you create a fetch() request to api/user.php and use "PUT" as the method
// Used to update list items
function PUT(Handler $handler)
{
}

function getUser(PDO $pdo, string $username, string $password)
{
    // $query = "SELECT * FROM users WHERE username=?;";
    $query = "CALL `getUser`(?);";

    $statement = $pdo->prepare($query);

    $statement->execute([$username]);

    $result = $statement->fetchAll();
    if (!$result) {
        throw new UserException(UserCode::USER_NOT_FOUND);
    }

    $user = $result[0];

    if (!password_verify($password, $user['password'])) {
        throw new UserException(UserCode::PASSWORD_DONT_MATCH);
    }

    return $user['user_id'];
}

function postUser(PDO $pdo, string $username, string $password)
{
    try {
        getUser($pdo, $username, $password);
        throw new UserException(UserCode::USER_EXISTS);
    } catch (UserException $error) {

        if ($error->getCode() != UserCode::USER_NOT_FOUND->value) {
            throw new UserException(UserCode::USER_EXISTS);
        }

        $password = password_hash($password, PASSWORD_DEFAULT);

        // $query = "INSERT INTO users (username, password) VALUES (:username, :password);";
        $query = "CALL `postUser`(:username, :password);";
        $statement = $pdo->prepare($query);

        $parameters = [":username" => $username, ":password" => $password];

        $statement->execute($parameters);

        $results = $statement->fetchAll();
        return $results[0]['user_id'];

    }
}
