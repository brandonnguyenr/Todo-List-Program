<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="background-image"></div>
    <div class="registration-section">
        <?php
        include("php/config.php");

        function sanitize_input($data) {
            return htmlspecialchars(trim($data));
        }

        if(isset($_POST['submit'])) {
            $username = sanitize_input($_POST['username']);
            $password = sanitize_input($_POST['password']);
            $confirm_password = sanitize_input($_POST['confirm_password']);

            if($password !== $confirm_password) {
                echo "<div class='message' style='color:red; font-size: 13px; font-family: Arial, sans-serif;'><p>Passwords do not match!</p></div><br>";
                echo "<a href='javascript:self.history.back()'><button type='button'>Restart</button></a>";                
            } else {
                $verify_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
                if(mysqli_num_rows($verify_query) != 0) {
                    echo "<div class='message' style='color:red; font-size: 13px; font-family: Arial, sans-serif;'><p>Username already exists! Please try a different username.</p></div><br>";
                    echo "<a href='javascript:self.history.back()'><button type='button'>Restart</button></a>";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT); 
                    mysqli_query($con, "INSERT INTO users(username, password) VALUES('$username', '$hashed_password')");
                    echo "<div class='message' style='color:green; font-size: 13px; font-family: Arial, sans-serif;'><p>Registration Successful!</p></div><br>";
                    echo "<a href='index.php'><button type='button'>Login Now</button></a>";                    
                }
            }
        }
        ?>
        <form method="POST" action="">
            <p>Create Account</p>
            <input type="text" name="username" id="username-field" class="login-form-field" placeholder="Username" required>
            <br>
            <input type="password" name="password" id="password-field" class="login-form-field" placeholder="Password" required>
            <br>
            <input type="password" name="confirm_password" id="confirm-password-field" class="login-form-field" placeholder="Confirm Password" required>
            <br>
            <button type="submit" name="submit" id="button">Register</button>
        </form>
    </div>
</body>

</html>
