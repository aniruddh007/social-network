<?php
include 'connection.php';
$insert = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!$conn) {
        die("Connection to this database failed due to" . mysqli_connect_error());
    }


    $image_loc = $_FILES['profile_picture']['tmp_name'];
    $image_name = $_FILES['profile_picture']['name'];
    // print_r($image_name);
    $user_id = $_POST['user_id'];
    $ext = pathinfo($image_name, PATHINFO_EXTENSION);
    $dest_add = "profile_images/" . $user_id . '.' . $ext;
    move_uploaded_file($image_loc, $dest_add);



    $full_name = $_POST['full_name'];
    $email = $_POST['user_id'];
    $password = $_POST['password'];
    $dob = $_POST['dob'];
    
    $hashed = password_hash($password , PASSWORD_DEFAULT);
    // sanitize user inputs 

    $user_id = mysqli_real_escape_string($conn, $user_id);
    $full_name = mysqli_real_escape_string($conn, $full_name);
    $email = mysqli_real_escape_string($conn, $email);
    // $password = mysqli_real_escape_string($conn, $password);
    $dob = mysqli_real_escape_string($conn, $dob);

    // image upload code 


    $sql = "INSERT INTO `user_info` (`user_id`, `full_name`,  `password`, `profile_pic`, `email_address` ,`dob` ) VALUES ('$user_id', '$full_name', '$hashed', '$dest_add' , '$email' , '$dob')";

    if ($conn->query($sql) === true ) { 
        $insert = true;
        header("Location: login.php");
        exit();
    } else {
        echo "Error : $sql <br> $conn->error";
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="signup.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<script>
    $(document).ready(function() {
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
                        <input type="file" id="file_upload" accept="image/*" name="profile_picture" required>
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