<?php
include 'connection.php';
include 'get_post.php';
include 'post_actions.php';
// $login = false;
session_start();

// is session created previously
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// fetch all details of the user
$user_id = $_SESSION['username'];
$sql = "select * from user_info where user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = $result->fetch_all();
$insert = false;
$conn = mysqli_connect($server, $username, $password, $database);
if (!$conn) {
    die("Connection to this database failed due to" . mysqli_connect_error());
}

// taking all the uploaded post by the user
$test = "select * from post where user_id = '$user_id' order by post_id desc";
$test_res = $conn->query($test);
$post_count = $test_res->fetch_all();
// print_r($post_count);
$count = sizeof($post_count);


// upload post and upload profile pic code
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_GET['action'] == 'add_post') {
        $allowedTypes = array("image/jpeg", "image/png", "image/gif");
        $maxSize = 5 * 1024 * 1024;
        $image_loc = $_FILES['post_pic']['tmp_name'];
        $image_name = $_FILES['post_pic']['name'];
        $ext = pathinfo($image_name, PATHINFO_EXTENSION);
        $img_size = $_FILES['post_pic']['size'];
        $img_type = $_FILES['post_pic']['type'];
        if (in_array($img_type, $allowedTypes) && $img_size <= $maxSize) {
            $dest_add = "posts/" . $user_id . $count . '.' . $ext;
            move_uploaded_file($image_loc, $dest_add);

            $caption = $_POST['caption'];
            $caption = mysqli_real_escape_string($conn, $caption);
            $sql = "INSERT INTO `post` (`post_id`, `user_id`, `caption`, `image`, `date` , `likes` , `dislikes`) VALUES (NULL, '$user_id', '$caption', '$dest_add', current_timestamp(),0,0)";
            if ($conn->query($sql) === true) {
                $insert = true;
            } else {
                echo "Error occur : $sql <br> $conn->error";
            }
            exit();
        } else {
            $response = array('success' => false, 'message' => 'Invalid file type or size.');
        }
    } else if (isset($_GET['action']) && $_GET['action'] == 'updatePic') {
        if (isset($_FILES['profile_pic'])) {
            $timestamp = time();
            $file = $_FILES['profile_pic'];
            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileType = $file['type'];
            $fileError = $file['error'];
            $targetDir = "profile_images/";
            $allowedTypes = array("image/jpeg", "image/png", "image/gif");
            $maxSize = 5 * 1024 * 1024;

            if (in_array($fileType, $allowedTypes) && $fileSize <= $maxSize && $fileError === 0) {
                $sql = "SELECT profile_pic FROM user_info WHERE user_id = '$user_id'"; // Adjust table and column names
                $result = mysqli_query($conn, $sql);

                if ($row = mysqli_fetch_assoc($result)) {
                    $oldImagePath = $row['profile_pic'];
                    // print_r($oldImagePath);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                    $newFileName = $user_id . "." . pathinfo($fileName, PATHINFO_EXTENSION); // userId.extension
                    $path = $timestamp. $newFileName;
                    $targetFile = $targetDir.$timestamp. $newFileName;
                    move_uploaded_file($fileTmpName, $targetFile);

                    $query = "UPDATE `user_info` SET `profile_pic` = '$targetFile' WHERE `user_info`.`user_id` = '$user_id'";
                    mysqli_query($conn, $query);
                    $response = array('newPic' => $path);
                    echo $path;
                    exit();
                } else {
                    $response = array('success' => false, 'message' => 'Error fetching old image path.');
                }
            } else {
                $response = array('success' => false, 'message' => 'Invalid file type or size.');
            }
        }
    }
}


// view post
if (isset($_GET['action']) && $_GET['action'] === 'get_posts') {
    ob_start();
    viewpost($conn, $user, $count, $post_count);
    exit();
}
if (mysqli_num_rows($result) > 0) {
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profile</title>
        <link rel="stylesheet" href="profile.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://kit.fontawesome.com/a7ebe64683.js" crossorigin="anonymous"></script>
        <script src="profile.js"></script>
    </head>

    <body>
        <p class="main_heading">Social Network</p>
        <div class="main_body">
            <div class="left">
                <div class="profile_card">
                    <div class="profile_photo">
                        <img src="<?php echo $user[0][3] ?>" id="profile_preview" alt="image">
                        <label for="upload_pic">
                            <span class="edit"><i class='fa-solid fa-pencil'></i></span>
                        </label>
                        <input type="file" id="upload_pic" accept="image/*" name="profile_picture" required>
                    </div>
                    <p class="mail_id"><?php echo $user[0][0] ?></p>
                    <div class="info">
                        <input type="text" name="info" id="info" placeholder="Intermediate">
                    </div>
                    <a href="#">Share Profile</a>
                </div>
            </div>
            <div class="right">
                <form id="add_post_card" name="add_post_card" action="profile.php" method="post" enctype="multipart/form-data">
                    <p class="text">Add Post</p>
                    <div class="post_area">
                        <textarea name="caption" id="caption"></textarea>
                        <img id="image_preview" src="tp.png">
                    </div>
                    <div class="post_button">
                        <button class="add_post" type="submit">Post</button>
                        <div class="add_image">
                            <label for="file_upload">
                                <span class="cur"><i class="fa-solid fa-image"></i> Add Image </span>
                            </label>
                            <input type="file" id="file_upload" accept="image/*" class="post_pic" name="post_pic" required>
                        </div>
                    </div>
                </form>
                <div class="posts">
                    <?php
                    if ($insert == false) {
                        viewpost($conn, $user, $count, $post_count);
                    }
                    ?>
                </div>
            </div>
        </div>
    </body>

    </html>
<?php
}
?>