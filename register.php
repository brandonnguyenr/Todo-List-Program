<?php
// include("php/config.php");
session_start();
$login_error = '';
$success_msg = '';

function sanitize_input($data)
{
    return htmlspecialchars(trim($data));
}

if (isset($_POST['submit'])) {
    $username = sanitize_input($_POST['username']);
    $password = sanitize_input($_POST['password']);
    $confirm_password = sanitize_input($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $login_error = 'Passwords do not match!';
    } else {
        $_POST['create'] = true;
        include('php/user.php');

        $data = $_SESSION['data'];
        if (isset($data['error'])) {
            $login_error = $data['error'] . '. Please Use A different Username.';
        } else {
            $success_msg = 'Registration Successful! Redirecting....';
        }
    }
}
?>

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
    <div class="login-section">
        <?php
        if (!empty($login_error)) {
            echo "<div class='message' style='color:red; font-size: 13px; font-family: Arial, sans-serif;'><p>$login_error</p></div><br>";
        }

        if (!empty($success_msg)) {
            echo "<div class='message' style='color:green; font-size: 13px; font-family: Arial, sans-serif;'><p>$success_msg</p></div><br>";

            echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 1000);</script>";
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
            <button class="btn btn-red" type="submit" name="submit">Register</button>
            <button onclick="window.location.href='index.php'" class="btn btn-blue" type="button">Back to Home</button>

    </div>
</body>

</html>