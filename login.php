<?php
$login = false;
$showError = false;
include 'connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!$conn) {
        die("Connection to this database failed due to" . mysqli_connect_error());
    }
    $username = $_POST["user_name"];
    $password = $_POST["password"];
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    $sql = "select password from user_info where user_id = '$username' ";
    $result = mysqli_query($conn, $sql);
    $result = mysqli_fetch_assoc($result);
    if(isset($result)){
    $hashed = $result['password'];
    // echo $hashed;
    if (password_verify($password, $hashed)){
        $login = true;
        session_start();
        $_SESSION["username"] = $username;
        echo $_SESSION["username"];
        header("Location: profile.php");
        exit();
    }
    else{
        echo "<script>alert('Incorrect Password'); </script>";
    }
}
else{
    echo "<script>alert('Please register ! Not a valid account '); </script>";
}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <div class="mainframe">
        <p class="main_heading">Social Network Login</p>
        <div class="box">
            <form action="login.php" method="post" class="form_area">
                <div>
                    <label for="email" class="heading">Email Address</label>
                    <br>
                    <input type="email" class="input_box" name="user_name" id="user_name">
                </div>
                <div>
                    <label for="password" class="heading">Password</label>
                    <br>
                    <input type="password" class="input_box" name="password" id="password">
                </div>
                <div>
                    <input class="button" type="submit" value="Login">
                </div>
                <div class="forget">Don't have an Account ? <a href="signup.php">Create Account</a></div>
            </form>
            <?php
            if ($login == true) {
                echo "<script>alert('Login Successful')</script>";
            }
            ?>
        </div>

    </div>
</body>

</html>