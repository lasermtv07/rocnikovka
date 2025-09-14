<?php
    function head(){
        echo '<hr>';
        echo '<h1>kotori</h1><hr>';
        echo '<a href="/">Home</a> <a href="login.php">Login</a> <a href="register.php">Register</a>';
        session_start();
        $nick=(isset($_SESSION["nick"]))?$_SESSION["nick"]:"anon";
        echo "<a href=\"/?logout\" style=float:right >$nick</a><hr>";
    }
    function connect(){
            //pripoj mysql
            $conn = new mysqli('localhost', 'misa', '');
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

?>