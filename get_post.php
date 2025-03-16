<?php
include 'connection.php';
function viewpost($Avtar, $count, $post_count)
{
    for ($it = 0; $it < $count; $it++) {
        $avtar = $Avtar;
        $pid = strval($post_count[$it][0]);
        $caption = strval($post_count[$it][2]);
        $address = strval($post_count[$it][3]);
        $date = strval($post_count[$it][4]);;
        $date = date('d-m-Y', strtotime($date));
        $like = strval($post_count[$it][5]);
        $dislike = strval($post_count[$it][6]);
        echo  "<div class='post_card' id='$pid'>";
        echo  "<div class='header_body'>";
        echo  "<div class='photo'> <img src='{$avtar}' id='image_preview' class = 'avtar'> </div>";
        echo  "<div class = 'caption'>$caption <p> posted on - {$date}</div>";
        echo  "<div class = 'close' > <i class='fa-solid fa-x'></i> </div>";
        echo "</div>";
        if( $address != 'NULL' ){
        echo  "<div class='visual_content'><img src='{$address}' id='image_preview'></div>";
        }
        echo "<div class='counter'>";
        echo "<a href='#' class='like-button' id='$pid'>";
        echo "Like   <i class='fa-regular fa-thumbs-up'>  $like</i>";
        echo "</a>";
        echo "<pre>          </pre>";
        echo "<a href='#' class='dislike-button' id='$pid'>";
        echo "Dislike   <i class='fa-regular fa-thumbs-down'>  $dislike</i></a></div>";
        echo  "</div>";
    }
}
?>
