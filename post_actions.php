<?php
$server = "localhost";
$username = "root";
$password = "";
$database = "social_network";
$conn = mysqli_connect($server, $username, $password, $database);
// delete post 
if (isset($_GET['action']) && $_GET['action'] == 'delete_post' && isset($_GET['id'])) {
    $p_Id = $_GET['id'];
    $p_Id = mysqli_real_escape_string($conn, $p_Id);
    $sql = "SELECT image FROM post WHERE post_id = '$p_Id'"; 
    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        $path = $row['image'];
        if ($path && file_exists($path)) {
            unlink($path);
        }
    }
    $q = "DELETE FROM post WHERE `post`.`post_id` = $p_Id";
    $conn->query($q);
    exit();
}

// dislike post
if (isset($_GET['action']) && $_GET['action'] == 'dislike' && isset($_GET['id'])) {
    $p_Id = $_GET['id'];
    $dislike = $_GET['dislikecount'];
    $like = $_GET['likecount'];
    $p_Id = mysqli_real_escape_string($conn, $p_Id);
    $dislike = mysqli_real_escape_string($conn, $dislike);
    $like = mysqli_real_escape_string($conn, $like);
    $q = "UPDATE `post` SET `likes` = '$like', `dislikes` = '$dislike' WHERE `post`.`post_id` = $p_Id";
    $conn->query($q);
    exit();
}

// like post
if (isset($_GET['action']) && $_GET['action'] == 'like' && isset($_GET['id'])) {
    $p_Id = $_GET['id'];
    $like = $_GET['likecount'];
    $dislike = $_GET['dislikecount'];
    $p_Id = mysqli_real_escape_string($conn, $p_Id);
    $dislike = mysqli_real_escape_string($conn, $dislike);
    $like = mysqli_real_escape_string($conn, $like);
    $q = "UPDATE `post` SET `likes` = '$like', `dislikes` = '$dislike' WHERE `post`.`post_id` = $p_Id";
    $conn->query($q);
    exit();
}
