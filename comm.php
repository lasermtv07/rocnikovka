<?php


    function connect(){
            require 'secrets.php';
            //pripoj mysql
            $conn = new mysqli('localhost', 'misa', $mysql_password);
            if ($conn->connect_error){
                echo "<b>Database error!</b>";
                die();
            }
            try {
                $conn->query('use chmelarmi22');
            }
            catch(e) {
                echo "<b>Error loading database</b>";
                die();
            }
            return $conn;
    }
    function isAdmin($id){
        try {
            $conn=connect();
            $r=$conn->query("SELECT isAdmin FROM accounts WHERE id = $id;");
            $r=$r->fetch_assoc();
            return ($r["isAdmin"])?true:false;
        } catch (Exception $e) {
            return false;
        }
    }

    function head(){
        session_start();
        echo '<hr>';
        echo '<h1>kotori</h1><hr>';
        echo '<a href="index.php">Home</a> <a href="login.php">Login</a> <a href="register.php">Register</a>';
        $nick=(isset($_SESSION["nick"]))?$_SESSION["nick"]:"anon";
        echo "<a href=\"index.php/?logout\" style=float:right >$nick";
        if(isAdmin($_SESSION["id"]))
            echo "<b style=color:red > [ADMIN]</b>";
        echo "</a><hr />";
    }

    function foot(){
        echo "<hr />(c) Michal Chmelar 2025. ";
        if(!isset($_COOKIE["visited"])){
            $t=file_get_contents('visits.txt');
            file_put_contents('visits.txt',(int)$t+1);
        }

        echo "<b>VISITED: ". file_get_contents('visits.txt');
        setcookie('visited',"true",time()+86400*30,"/");
    }
    function listTweets($user){
        $conn=connect();
        echo "<br>";
        if($user=="")
            $stmt=$conn->query("SELECT tweets.*,accounts.username FROM tweets INNER JOIN accounts ON tweets.authorID = accounts.id ORDER BY id DESC");
        else
            $stmt=$conn->query("SELECT tweets.*,accounts.username FROM tweets INNER JOIN accounts ON tweets.authorID = accounts.id WHERE accounts.id = $user ORDER BY id DESC");

        while($i = $stmt->fetch_assoc()){
            session_start();
            echo "<b><a href=/profile.php?user=".$i['authorID']." >".$i['username']."</a></b> - ".$i['postTime'];
            if(isAdmin($_SESSION["id"]))
                echo "<a style=color:red;float:right href=delete.php?id=".$i['id'].">Delete</a>";
            echo "<p>".$i['text']."</p><hr>";
        }
    }
?>