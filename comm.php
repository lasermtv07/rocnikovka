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
        echo '<link rel=stylesheet href=css/style.css />';
        echo "<div id=head>";
        echo '<center>';
        echo '<h1>小鳥</h1><span>';
        echo '<h2><a href="index.php">Home</a></h2> <h2><a href="login.php">Login</a></h2> <h2><a href="register.php">Register</a></h2>';

        echo "<h2><a href=\"index.php/?logout\" >Logout</a></h2>";
        echo '<h2>';
        if(in_array("?",str_split($_SERVER['REQUEST_URI'])))
            echo "<a href=".$_SERVER['REQUEST_URI']."&";
        else
            echo "<a href=".$_SERVER['REQUEST_URI']."?";

        if(isset($_COOKIE["light"]))
            echo "change>dark</a>";
        else
            echo "change>light</a>";
        echo '</h2>';
        $nick=(isset($_SESSION["nick"]))?$_SESSION["nick"]:"anon";
        echo "</span>";
        if(isset($_COOKIE["light"])){
            echo "<link rel=stylesheet href=\"css/light.css\" />";
        }
        if(isset($_GET["change"])){
            if(!isset($_COOKIE["light"])){
                setcookie("light","1",time()+3600*24*30);
            }
            else {
                setcookie("light",!$_COOKIE["light"],time()+3600*24*30);
            }
            $out_url=str_replace("?change","",$_SERVER['REQUEST_URI']);
            $out_url=str_replace("&change","",$out_url);
            header('location:'.$out_url);
        }
        echo "</center></div>";

    }

    function foot(){
        echo "<div id=foot><hr />(c) Michal Chmelar 2025. ";
        if(!isset($_COOKIE["visited"])){
            $t=file_get_contents('visits.txt');
            file_put_contents('visits.txt',(int)$t+1);
        }

        echo "<b>VISITED: ". file_get_contents('visits.txt');
        setcookie('visited',"true",time()+86400*30,"/");
        echo "</div>";
    }
    function listTweets($user,$changeFeed=false){
        echo "<script src=js/like.js ></script>";
        $conn=connect();
        echo "<br>";
        if(!$changeFeed || $user==NULL){
            if($user=="") //pokud generuje homepage (discover)
                $stmt=$conn->query("SELECT tweets.*,accounts.username FROM tweets INNER JOIN accounts ON tweets.authorID = accounts.id ORDER BY id DESC");
            else //pokud generuje stranku uzivatele
                $stmt=$conn->query("SELECT tweets.*,accounts.username FROM tweets INNER JOIN accounts ON tweets.authorID = accounts.id WHERE accounts.id = $user ORDER BY id DESC");
        } else { // homepage ale following feed
            $subStmt=$conn->query("SELECT followedID FROM follows WHERE followerID='$user'");
            $stmtText="($user";
            while($i=$subStmt->fetch_assoc()){
                $stmtText.=",";
                $stmtText.=$i["followedID"];
            }
            $stmtText.=")";
            $stmt=$conn->query("SELECT tweets.*,accounts.username FROM tweets INNER JOIN accounts ON tweets.authorID = accounts.id WHERE accounts.id IN $stmtText ORDER BY id DESC");
            
        }
        while($i = $stmt->fetch_assoc()){
            session_start();
            echo "<b><a href=profile.php?user=".$i['authorID']." >".$i['username']."</a></b> - ".$i['postTime'];
            //mazani
            if(isAdmin($_SESSION["id"]) || $_SESSION["id"]==$i['authorID'])
                echo "<a style=color:red;float:right href=delete.php?id=".$i['id'].">Delete</a>";
            echo "<p>".$i['text']."</p>";
            //obrazek
            if($i["picture"])
                echo "<img src=images/".$i["picture"]." class=post_img />";
            //liky
            $likeCount=mysqli_num_rows($conn->query("SELECT * FROM likes WHERE tweetID=".$i["id"]));
            //barveni pro liky ktere udelil uzivatel
            if(isset($_SESSION["id"])){
                $r=$conn->query("SELECT * FROM likes WHERE userID=".$_SESSION["id"]." AND tweetID=".$i["id"]);
                $count=mysqli_num_rows($r);
            } else
                $count=0;
            $color="";
            if($count>0)
                $color=" liked";

            echo "<br><span style=font-size:2em;padding:5%$color >";
            echo "<span id=\"lc".$i['id']."\"onclick=addLike('like.php?id=".$i['id']."&ret=$user',".$i["id"].",".((isset($_SESSION["id"]))?'true':'false').") class=\"like$color\">♥ <span id=l".$i["id"]. " style=\"color:var(--fg) !important;font-size:1.25rem;\">$likeCount</span></span></span>";
            echo $_SESSION["id"];
            echo "<hr>";
        }
    }

?>