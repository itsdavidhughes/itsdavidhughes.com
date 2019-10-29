<?php
    require_once("morse/Post.Class.php");
    $postHelper = new Post();
    $post = [];
    $route = "";
    $homepage = true;
    // Set how many posts to show on the homepage
    $show_full_posts = 3;
    // Set how many posts to show in the recent posts lists
    $show_recent_posts = 3;

    if(isset($_GET['route']) && trim($_GET['route']) != ''){
        $homepage = false;
        $route = $_GET['route'];
        $post = $postHelper->loadPostByRoute($route);
        $nextPost = $postHelper->getNextPost($post['route']);
        $prevPost = $postHelper->getPrevPost($post['route']);
    } else {
        $post = $postHelper->getPostsDetail($route, $show_full_posts);
        $nextPost = $postHelper->getNextPost($post[0]['route']);
        $prevPost = $postHelper->getPrevPost(end($post)['route']);
        reset($post);
    }

    $latestPosts = [];
    $displayRecentPost = true;

    if($displayRecentPost){
        $latestPosts = $postHelper->getLatestPosts($route, $show_full_posts-1, $show_recent_posts);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= ($homepage)?'':$post['blog_title']." : " ?>Your Blog Name</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style type="text/css" media="screen">
		body {
			font-family: sans-serif;
            font-size: 100%;
            padding: 40px 56px;
		}
        #posts {
            max-width: 800px;
        }
	</style>
</head>

<body>

    <header>
        <h1><a href="/demo">Your Blog Name</a></h1>
    </header>

    <section id="posts">

    <?php

        // Show homepage post(s), set with $show_full_posts

        if($homepage){
            foreach ($post as $key => $value) {
                ?>
                <h2><a href="<?= $value['route'] ?>"><?= $value['blog_title'] ?></a></h2>
                <?= $value['html'] ?>
                <p class="posted"><a href="<?=$value['route']?>">Posted <?= date('F jS Y', strtotime($value['date_created']))?></a></p>
                <?php
            }
        }

        // Single post detail pages

        else {
            ?>
            <h2><a href="<?= $post['route'] ?>"><?= $post['blog_title'] ?></a></h2>
            <?= $post['html'] ?>
            <p class="posted"><a href="<?=$post['route']?>">Posted <?= date('F jS Y', strtotime($post['date_created']))?></a></p>

            <?php
                if(isset($nextPost)){
                    ?>
                        <p><a href="<?= $nextPost["route"] ?>">Next >> <?= $nextPost["blog_title"] ?></a></p>
                    <?php
                }
            ?>

            <?php
                if(isset($prevPost)){
                    ?>
                        <p><a href="<?= $prevPost["route"] ?>"><?= $prevPost["blog_title"] ?> << Prev </a></p>
                    <?php
                }
            ?>

            <?php
        }

    ?>

    <?php

        // Recent posts, respects and skips $show_full_posts on homepage

        if($latestPosts){
            echo "<h3>Recent Posts</h3>";
            echo "<ul>";
            $date=date('Y-m-d');
            foreach($latestPosts as $file_name){
                $post_details = $postHelper->parseFileName($file_name);?>
                <li><a href="<?= $post_details['route'] ?>"><?= $post_details['blog_title'] ?></a></li>
                <?php
            }
            echo "<li><a href=\"/demo/archive\">View all posts</a></li>";
            echo "</ul>";
        }
    ?>

    </section>

</body>
</html>
