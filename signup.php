<?php
include 'connection.php';
$insert = false;

class User
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function register($full_name, $email, $password, $dob, $image_loc, $image_name)
    {
        $full_name = $this->db->escapeString($full_name);
        $email = $this->db->escapeString($email);
        $dob = $this->db->escapeString($dob);

        $q = "select * from user_info where user_id = '$email'";
        $result = $this->db->query($q);
        $result = mysqli_fetch_assoc($result);


        if ( $result ){
            // echo "Inside block ";
            return false ;
        } else {
            $ext = pathinfo($image_name, PATHINFO_EXTENSION);
            $dest_add = "profile_images/" . $email . '.' . $ext;

            if (move_uploaded_file($image_loc, $dest_add)) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO `user_info` (`user_id`, `full_name`, `password`, `profile_pic`, `email_address`, `dob`) 
                    VALUES ('$email', '$full_name', '$hashed', '$dest_add', '$email', '$dob')";

                if ($this->db->query($sql)) {
                    return true; // Registration successful
                } else {
                    // Handle database error
                    echo "Error: " . $this->db->getConnection()->error;
                    return false;
                    
                }
            } else {
                return false; // Image upload failed
            }
        }
    }
}

// Instantiate Database and User objects
$db = new Database();
$user = new User($db);

$insert = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $image_loc = $_FILES['profile_picture']['tmp_name'];
    $image_name = $_FILES['profile_picture']['name'];
    $full_name = $_POST['full_name'];
    $email = $_POST['user_id'];
    $password = $_POST['password'];
    $dob = $_POST['dob'];

    if ($user->register($full_name, $email, $password, $dob, $image_loc, $image_name)) {
        $insert = true;
        header("Location: login.php");
        exit();
    } else {
        echo "<script>alert('Registration failed. Please check the form and try again.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="signup.css">
    <script src="jquery-3.6.0.min.js"></script>
</head>
<script>
    $(document).ready(function() {
        // date not exceed the current date 
        const today = new Date().toISOString().split('T')[0];
        $('#dob').attr('max', today);
        
        $("#file_upload").change(function() {
            if (this.files && this.files[0]) {
                let reader = new FileReader();

                reader.onload = function(e) {
                    $('#image_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
        $(".form-area").submit(function(event) {
            const password = $('#password').val();
            const confirm = $('#confirm').val();
            const date = $('#dob').val();
            let dob = new Date(date);
            let current = new Date();
            let age = current.getFullYear() - dob.getFullYear();
            let monthdiff = current.getMonth() - dob.getMonth();
            if (monthdiff < 0 || (monthdiff === 0 && current.getDate() < date.getDate())) {
                age--;
                if (age < 18) {
                    alert("your age should be greater than 18 years");
                    event.preventDefault();
                }
            }
            if (password != confirm) {
                alert("Password do not match !");
                event.preventDefault();
            } else {
                const strong = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/;
                if (!strong.test(password)) {
                    alert("Password must contain at least one uppercase letter, one lowercase letter, one number, and one symbol, and be at least 6 characters long.");
                    event.preventDefault();
                }
            }
        });
    });
</script>

<body>
    <div class="main-area">
        <span class="main-heading">Join Social Network</span>
        <div class="frame">
            <form method="post" class="form-area" enctype="multipart/form-data">
                <div class="profile">
                    <img src="profile.png" id="image_preview" alt="">
                    <div class="upload">
                        <label for="file_upload">
                            <span class="cur">Choose Profile Pic </span>
                        </label>
                        <input type="file" id="file_upload" accept="image/*" name="profile_picture">
                    </div>
                </div>
                <div class="fullname">
                    <label for="fullname">Full Name</label>
                    <input type="text" name="full_name" id="full_name" required>
                </div>
                <div class="dob">
                    <label for="dob">Date of Birth</label>
                    <input type="date" name="dob" id="dob" required>
                </div>
                <div class="email">
                    <label for="email">Email Address</label>
                    <input type="email" name="user_id" id="user_id" required>
                </div>
                <div class="pass">
                    <div class="left">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" required>
                        <p id="pass-text">
                            Use A-Z , a-z , 0-9 , !@#$%^&* in password
                        </p>
                    </div>
                    <div class="right">
                        <label for="re_password">Re - Password</label>
                        <input type="password" name="confirm" id="confirm" required>
                    </div>
                </div>
                <div class="btn">
                    <button> Sign Up</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>