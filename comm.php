<?php
    function head(){
        echo '<hr>';
        echo '<h1>kotori</h1><hr>';
        echo '<a href="index.php">Home</a> <a href="login.php">Login</a> <a href="register.php">Register</a>';
        session_start();
        $nick=(isset($_SESSION["nick"]))?$_SESSION["nick"]:"anon";
        echo "<a href=\"index.php/?logout\" style=float:right >$nick</a><hr>";
    }

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

    function foot(){
        echo "<hr />(c) Michal Chmelar 2025. ";
        if(!isset($_COOKIE["visited"])){
            $t=file_get_contents('visits.txt');
            file_put_contents('visits.txt',(int)$t+1);
        }

        echo "<b>VISITED: ". file_get_contents('visits.txt');
        setcookie('visited',"true",time()+86400*30,"/");
    }
?>