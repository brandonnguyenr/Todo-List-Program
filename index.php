<?php
session_start();

$login_error = '';

if (isset($_POST['submit'])) {
    include("php/user.php");

    $data = $_SESSION['data'];
    if (isset($data['error'])) {
        $login_error = $data['error'] . '. Please Try Again.';
    } else {
        $_SESSION['ID'] = $data['user_id'];
        // TODO: maybe use javascript to use window.replace('todo.html') instead 
        // so users can't use the back button to go back to login 
        header('Location: sql/todopage.html');
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="background-image"></div>
    <div class="login-section">
        <h2 id="login-header"></h2>
        <form id="login-form" method="POST" action="">
            <p>TASK MANAGER</p>
            <input type="text" name="username" id="username-field" class="login-form-field" placeholder="Username">
            <br>
            <input type="password" name="password" id="password-field" class="login-form-field" placeholder="Password">
            <br>
            <form action="todopage.html" method="get">
                <button type="submit" id="button-register">Login</button>
            </form>
        </form>

        <?php
        if (!empty($login_error)) {
            echo "<p style='color:red; font-size: 10px; font-family: Arial, sans-serif;'>$login_error</p>";
        }
        ?>

        <div class="black-bar"></div>
        <p>NEW USER</p>
        <a href="register.php"><button class="btn btn-red" type="button">Register</button></a>
    </div>
</body>

</html>