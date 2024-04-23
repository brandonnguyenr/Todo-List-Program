<?php
session_start();
include("php/config.php");

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($con, $_POST["username"]);
    $password = $_POST["password"];

    $stmt = $con->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            header("Location: index.php");
            exit();
        } else {
            $login_error = "Invalid username or password. Please try again.";
        }
    } else {
        $login_error = "Invalid username or password. Please try again.";
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
        <form id="login-form" method="POST">
            <p>TASK MANAGER</p>
            <input type="text" name="username" id="username-field" class="login-form-field" placeholder="Username">
            <br>
            <input type="password" name="password" id="password-field" class="login-form-field" placeholder="Password">
            <br>
            <!-- <button type="submit" name="submit" id="button-register">Login</button> -->
            <button class="btn btn-blue" type="submit" name="submit">Login</button>
        </form>

        <?php
        if (!empty($login_error)) {
            echo '<p style="color:red; font-size: 10px; font-family: Arial, sans-serif;">' . $login_error . '</p>'; 
        }
        ?> 
        
        <div class="black-bar"></div>
        <p>NEW USER</p>
        <!-- <a href="register.php"><button type="button" id="button">Register</button></a> -->
        <a href="register.php"><button class="btn btn-red" type="button">Register</button></a>
    </div>
</body>

</html>

