<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    require_once("morse/Post.Class.php");
    $postHelper = new Post();
    $all_posts = $postHelper->getAllPosts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Archive</title>
</head>
<body>
    <ul>
        <?php
            foreach($all_posts as $file_name){
                $post_details = $postHelper->parseFileName($file_name)
                ?>
                <li><a href="<?= $post_details['route'] ?>"><?= $post_details['blog_title'] ?></a> <?= date('F jS Y', strtotime($post_details['date_created']))?></li>
                <?php
            }
        ?>
    </ul>
</body>
</html>
