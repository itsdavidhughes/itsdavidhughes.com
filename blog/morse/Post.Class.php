<?php
require_once("Parsedown.php");

class Post
{
    public $file_name; // Full File name
    public $dir = "posts"; // Posts Directory
    public $parser; // Markdown Parser 

    function __construct(){
        $this->parser = new Parsedown();
    }

    //Get a post by route
    public function loadPostByRoute($route){
        $all_posts = $this->getAllPosts();
        $post_data = [];

        foreach($all_posts as $post)
        {
            if (strpos($post, $route) !== false) {
                $this->file_name=$post;
                break;
            }
        }

        if(isset($this->file_name) && !empty($this->file_name)){
            $post_data = $this->parseFileName($this->file_name,true);
        }

        return $post_data;
    }

    //Get a lastest post
    public function getLatestPost(){
        $posts = $this->getAllPosts();
        $posts = array_slice($posts, 0,1 , true);
        // echo '<pre>',print_r($posts);
        $post_data = [];

        if(count($posts) > 0){
            $this->file_name = $posts[0];
            $post_data = $this->parseFileName($this->file_name,true);
        }
       
        return $post_data;
    }

    //Get N latest posts and skip a route or first file
    public function getLatestPosts($skipRoute = "",$offset = 0,$total = 5){
        $posts = $this->getAllPosts();
        $result = [];

        if($skipRoute != ""){
            
            foreach($posts as $post){
                if (strpos($post, $skipRoute) === false) {
                    $result[] = $post;
                }
            }

            $result = array_slice($result, $offset,$total , true);
        }else{
            $posts = array_slice($posts, $offset+1,$total , true);
            return $posts;
        }

        return $result;
    }

    //Get all posts
    public function getAllPosts($limit = 0){
        $result=[];
        $postsFinal=[];
        $posts = array_diff(scandir($this->dir,SCANDIR_SORT_DESCENDING), array('..', '.'));
        $date=date('Y-m-d');
        
        if($posts){
            foreach($posts as $key=> $post)
            {
                $post = trim($post);
                if($post[0] == '-') continue;
                $result[]=$this->parseFileName($post);
            }
        }
        
        if($result)
        {
            foreach($result as $key => $fileresult)
            {
                if(strtotime($fileresult['date_created']) <= strtotime($date))
                {
                    $postsFinal[]=$posts[$key];
                }
            }
        }

        if($limit > 0){
            $postsFinal = array_slice($postsFinal, 0,$limit , true);
        }

        
        return $postsFinal;
    }

    //Get details of a file
    public function parseFileName($file_name,$content = true)
    {
        $date = strtok($file_name, '-');
        $date_created = $this->dateFormat($date);
        $hyphenposition = strpos($file_name,'-');
        $withextension = substr($file_name,$hyphenposition+1);
        $route = substr($withextension, 0, strrpos($withextension, "."));
        $title = ucwords(str_replace('-',' ',$route));

        $post  = array(
            'date_created' => $date_created,
            'route' => $route,
            'blog_title' => $title
        );

        if($content){
            $file_path = $this->dir.'/'.$file_name;
            $content = file_get_contents($file_path);
            $first_line = strtok($content, "\n");
            if(strpos($first_line,'[title]') !== false){
                $first_line =str_replace('[title]','',$first_line);
                $post['blog_title'] = $first_line;
                $content= preg_replace('/^.+\n/', '', $content);
            }
            
          
            $post['html'] = $this->parser->text($content);
            // echo '<pre>',print_r($post);exit;
        }
    
        return $post;
    }

    //Format give data
    public function dateFormat($date)
    {
        $year = substr($date, 0, 4);
        $month = substr($date, 4, 2);
        $date = substr($date, 6, 2);
        $formated_date = date('Y-m-d',strtotime("$year-$month-$date"));

        return $formated_date;
    }

    //Next post from given route
    public function getNextPost($route){
        $posts = $this->getAllPosts();
        $targetPost = "";
        $post_found = false;

        foreach($posts as $post){
            if (strpos($post, $route) !== false) {
                $post_found = true;
                break;
            }

            if(!$post_found){
                $targetPost = $post;
            }
        }
        if($targetPost == ""){
            return null;
        }
        
        $post = $this->parseFileName($targetPost);
        
        return $post; 
    }

    //Prev post from given route
    public function getPrevPost($route){
        $posts = $this->getAllPosts();
        $targetPost = "";
        $post_found = false;

        foreach($posts as $post){
            

            if($post_found){
                $targetPost = $post;
                break;
            }

            if (strpos($post, $route) !== false) {
                $post_found = true;
            }
        }

        if($targetPost == ""){
            return null;
        }

        $post = $this->parseFileName($targetPost);
        
        return $post; 
    }

    public function getPostsDetail($route, $limit)
    {
        $posts = $this->getLatestPosts($route,-1, $limit);
        $posts_data = [];
        foreach ($posts as $post) {
            $posts_data[] = $this->parseFileName($post,true);
        } 
        // echo "<pre>",print_r($posts_data);
        
        return $posts_data;
        
    }

}
