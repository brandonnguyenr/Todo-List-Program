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

        <?php
        session_start();
        include("php/config.php");
        if (isset($_POST['submit'])) {
            $username = mysqli_real_escape_string($con, $_POST["username"]);
            $password = mysqli_real_escape_string($con, $_POST["password"]);

            $result = mysqli_query($con, "SELECT * FROM users WHERE username='$username' AND password='$password' ") or die("ERROR");
            $row = mysqli_fetch_assoc($result);

            if (is_array($row) && !empty($row)) {
                $_SESSION['id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
            } else {
                $login_error = "Invalid username or password. Please try again.";  
            }
        }
        ?>

        <h2 id="login-header"></h2>
        <form id="login-form" method="POST">
            <p>TASK MANAGER</p>
            <input type="text" name="username" id="username-field" class="login-form-field" placeholder="Username">
            <br>
            <input type="password" name="password" id="password-field" class="login-form-field" placeholder="Password">
            <br>
            <button type="submit" name="submit" id="button-register">Login</button>
        </form>

        <?php
        if (!empty($login_error)) {
            echo '<p style="color:red; font-size: 10px; font-family: Arial, sans-serif;">' . $login_error . '</p>'; 
        }
        ?> 
        
        <div class="black-bar"></div>
        <p>NEW USER</p>
        <button type="submit" name="submit" id="button">Register</button>
    
    </div>
</body>

</html>



