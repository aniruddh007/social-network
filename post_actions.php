<?php

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
    $count = $_GET['count'];
    $p_Id = mysqli_real_escape_string($conn, $p_Id);
    $count = mysqli_real_escape_string($conn, $count);
    $q = "UPDATE `post` SET `dislikes` = $count WHERE `post`.`post_id` = $p_Id;";
    $conn->query($q);
    exit();
}

// like post
if (isset($_GET['action']) && $_GET['action'] == 'like' && isset($_GET['id'])) {
    $p_Id = $_GET['id'];
    $count = $_GET['count'];
    $p_Id = mysqli_real_escape_string($conn, $p_Id);
    $count = mysqli_real_escape_string($conn, $count);
    $q = "UPDATE `post` SET `likes` = $count WHERE `post`.`post_id` = $p_Id;";
    $conn->query($q);
    exit();
}
