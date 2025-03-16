<?php
include 'get_post.php';
include 'post_actions.php';
session_start();
$user_id = $_SESSION['username'];
class User
{
    private $db;
    private $user_id;
    private $userData;

    public function __construct(Database $db)
    {
        $this->db = $db;
        if (!isset($_SESSION['username'])) {
            header("Location: login.php");
            exit();
        }
        $this->user_id = $_SESSION['username'];
        $this->loadUserData();
    }

    public function updateName($name)
    {
        $name = $this->db->escapeString($name);
        $query = "UPDATE user_info SET full_name = '$name' WHERE user_id = '$this->user_id'";
        $this->db->query($query);
    }

    private function loadUserData()
    {
        $sql = "SELECT * FROM user_info WHERE user_id = '$this->user_id'";
        $result = $this->db->query($sql);
        $this->userData = mysqli_fetch_assoc($result);
        if (!$this->userData) {
            die("User not found.");
        }
    }

    public function getUserData()
    {
        return $this->userData;
    }

    public function updateProfilePic($file)
    {
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
                $sql = "SELECT profile_pic FROM user_info WHERE user_id = '$this->user_id'"; 
                $result = $this->db->query($sql);

                if ($row = mysqli_fetch_assoc($result)) {
                    $oldImagePath = $row['profile_pic'];
                    // print_r($oldImagePath);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                    $newFileName = $this->user_id . "." . pathinfo($fileName, PATHINFO_EXTENSION); // userId.extension
                    $path = $timestamp . $newFileName;
                    $targetFile = $targetDir . $timestamp . $newFileName;
                    move_uploaded_file($fileTmpName, $targetFile);

                    $query = "UPDATE `user_info` SET `profile_pic` = '$targetFile' WHERE `user_info`.`user_id` = '$this->user_id'";
                    $this->db->query($query);
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

// Post Class
class Post
{
    private $db;
    private $user_id;

    public function __construct(Database $db, $user_id)
    {
        $this->db = $db;
        $this->user_id = $user_id;
    }

    // function to add post 
    public function addPost($caption, $file)
    {
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileType = $file['type'];
        $allowedTypes = array("image/jpeg", "image/png", "image/gif");
        $maxSize = 5 * 1024 * 1024;

        if (in_array($fileType, $allowedTypes) && $fileSize <= $maxSize) {
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $timestamp = time();
            $dest_add = "posts/" . $this->user_id . $timestamp . '.' . $ext;
            if (move_uploaded_file($fileTmpName, $dest_add)) {
                $caption = $this->db->escapeString($caption);
                $sql = "INSERT INTO `post` (`post_id`, `user_id`, `caption`, `image`, `date`, `likes`, `dislikes`) 
                        VALUES (NULL, '$this->user_id', '$caption', '$dest_add', current_timestamp(), 0, 0)";
                return $this->db->query($sql);
            }
        }
        return false;
    }

    public function addPostwithoutPic($caption)
    {
        $caption = $this->db->escapeString($caption);
        $sql = "INSERT INTO `post` (`post_id`, `user_id`, `caption`, `image`, `date`, `likes`, `dislikes`) 
                        VALUES (NULL, '$this->user_id', '$caption', 'NULL', current_timestamp(), 0, 0)";
        return $this->db->query($sql);
    }

    // get all the posts done by the user 
    public function getPostCount()
    {
        $test = "SELECT * FROM post WHERE user_id = '$this->user_id' order by post_id DESC" ;
        $result = $this->db->query($test);
        $allpost = $result->fetch_all();
        return $allpost;
    }
}


// Instantiate Database, User, and Post objects
$db = new Database();
$user = new User($db);
$userData = $user->getUserData();
$post = new Post($db, $user_id);
$post_count = $post->getPostCount();
$count = sizeof($post_count);
$insert = false;

if (isset($_GET['action']) && $_GET['action'] == 'updateName' && isset($_GET['name'])) {
    $user->updateName($_GET['name']);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_GET['action'] == 'add_post') {
        $caption = $_POST['caption'];
        if ($_FILES['post_pic']['name'] != '') {
            if ($post->addPost($caption, $_FILES['post_pic'])) {
                $insert = true;
            } else {
                echo "<script>alert('Error uploading post. Please check the file and try again.');</script>";
            }
        }
        else{
            $post->addPostwithoutPic($caption);
            $insert = true ;
        }

        
        exit();
    } else if (isset($_GET['action']) && $_GET['action'] == 'updatePic') {
        $user->updateProfilePic($_FILES['profile_pic']);
        exit();
    }
}
if (isset($_GET['action']) && $_GET['action'] === 'get_posts') {
    ob_start();
    viewpost($userData['profile_pic'], $count, $post_count);
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="profile.css">
    <script src="jquery-3.6.0.min.js"></script>
    <script src="a7ebe64683.js" crossorigin="anonymous"></script>
    <script src="profile.js"></script>
</head>

<body>
    <p class="main_heading">Social Network</p>
    <div class="main_body">
        <div class="left">
            <div class="profile_card">
                <div class="profile_photo">
                    <img src="<?php echo $userData['profile_pic'] ?>" id="profile_preview" alt="image">
                    <label for="upload_pic">
                        <span class="edit"><i class='fa-solid fa-pencil'></i></span>
                    </label>
                    <input type="file" id="upload_pic" accept="image/*" name="profile_picture" required>
                </div>
                <div class='text_box' id='name'>
                    <span class='valu'><?php echo $userData['full_name'] ?></span>
                    <input type='text' value='<?php echo $userData['full_name'] ?>'>
                    <a class='savename save'> <i class='fa-solid fa-floppy-disk'></i>
                    </a>
                    <a class='edit'><i class='fa-solid fa-pencil'></i></a>
                </div>
                <p class="mail_id"><?php echo $userData['user_id'] ?></p>
                <a class = "share" href="#">Share Profile</a>
            </div>
        </div>
        <div class="right">
            <form id="add_post_card" name="add_post_card" action="profile.php" method="post" enctype="multipart/form-data">
                <p class="text">Add Post</p>
                <div class="post_area">
                    <textarea name="caption" id="caption"></textarea>
                </div>
                <div class="post_button">
                    <button class="add_post" type="submit">Post</button>
                    <div class="add_image">
                        <label for="file_upload">
                            <span class="cur"><i class="fa-solid fa-image"></i> Add Image </span>
                        </label>
                        <input type="file" id="file_upload" accept="image/*" class="post_pic" name="post_pic">
                    </div>
                </div>
            </form>
            <div class="posts">
                <?php
                if ($insert == false) {
                    viewpost($userData['profile_pic'], $count, $post_count);
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>
<?php
// }
?>