<?php
$login = false;
$showError = false;
include 'connection.php';
class User {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function login($username, $password) {
        $username = $this->db->escapeString($username);

        $sql = "SELECT password FROM user_info WHERE user_id = '$username'";
        $result = $this->db->query($sql);
        $result = mysqli_fetch_assoc($result);

        if (isset($result)) {
            $hashedPassword = $result['password'];
            if (password_verify($password, $hashedPassword)) {
                session_start();
                $_SESSION["username"] = $username;
                return true; // Login successful
            }
        }
        return false; // Login failed
    }
}


// Instantiate Database and User objects
$db = new Database();
$user = new User($db);

$loginSuccessful = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["user_name"];
    $password = $_POST["password"];

    if ($user->login($username, $password)) {
        $loginSuccessful = true;
        header("Location: profile.php");
        exit();
    } else {
        echo "<script>alert('Incorrect username or password.');</script>";
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