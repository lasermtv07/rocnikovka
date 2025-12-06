<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>comments :: kotori</title>
</head>
<body>
<?php 
require 'comm.php';
head();    
?>
    <main>
        <h1>comments</h1>
        <hr class=delim />
        <?php 
        //vstupni bufferovani - aby fungovalo resetovani headeru
        //uklada vypsana data do bufferu, aby mohly byt headery upraveny behem psani do souboru
        ob_start();
            session_start();
            $conn=connect();
            //vypise tweet kterej komentujeme
            $stmt=$conn->prepare("SELECT tweets.*,accounts.username,accounts.picture as pfp FROM tweets INNER JOIN accounts ON tweets.authorID=accounts.id WHERE tweets.id=?");
            $stmt->bind_param("i",$_GET["tweet"]);
            $stmt->execute();
            $stmt=$stmt->get_result();
            $stmt=$stmt->fetch_assoc();
            //var_dump($stmt);
            if($stmt==NULL){
                echo "<b>Error: tweet doesn't exist!</b>";
                die();
            }

            $id=$stmt['id'];
            $authorID=$stmt['authorID'];
            $username=$stmt['username'];
            $text=$stmt['text'];
            $postTime=$stmt['postTime'];
            $picture=$stmt['picture'];
            $quote=$stmt['quote'];
            $pfp=$stmt['pfp'];

            printOneTweet($id,$authorID,$username,$text,$postTime,$picture,$quote,$pfp,$conn);
            echo "<b>".$_SESSION["nick"]."</b>";
        ?>
        <form method=POST>
            <textarea name="text" style=width:99%;height:100px;></textarea>
            <input type=submit name=s value="Post" />
        </form>
        <script src=js/like.js ></script>
        <?php
        if(isset($_POST["s"]) && isset($_SESSION["id"])){
            $cont=true;
            $comment=$_POST["text"];

            //validuj komentar
            if($comment==""){
                $cont=false;
                errorBox("<b>Error: Comment cannot be empty!</b>");
            }
            if(mb_strlen($comment)>255 && $cont){
                $cont=false;
                errorBox("<b>Error: Comment cannot be longer than 255 characters!</b>"); 
            }
            if(containsSwears($comment)){
                $cont=false;
                errorBox("Error: cannot contain swears!");
            }

            if($cont){
                $comment=htmlspecialchars($comment);
                $stmt=$conn->prepare("INSERT INTO comments (tweetID,authorID,text) VALUES (?,?,?)");
                $stmt->bind_param("iis",$_GET["tweet"],$_SESSION["id"],$comment);
                $stmt->execute();
                unset($_POST);
                header_remove('s');
                header('location: '.$_SERVER["REQUEST_URI"]);
            }
        }

        //vypis komenty
        $stmt=$conn->prepare("SELECT comments.*,accounts.username,accounts.picture FROM comments INNER JOIN accounts ON accounts.id=comments.authorID WHERE tweetID=? ORDER BY comments.id DESC");
        $stmt->bind_param("i",$_GET["tweet"]);
        $stmt->execute();
        $stmt=$stmt->get_result();
        while($i=$stmt->fetch_assoc()){
            $id=$i["id"];
            $nick=$i["username"];
            $comment=$i["text"];
            $authorID=$i["authorID"];
            if($i["picture"]=="")
                $picture="pfp/default.png";
            else
                $picture=$i["picture"];
            
            echo "<hr />";
            echo "<img src=$picture style=\"display:inline-block;border: 2px solid var(--fg);\" width=25 /> ";
            echo "<b style=position:relative;top:-10px;><a href=profile.php?user=$authorID>$nick</a></b>";
            if(isAdmin($_SESSION["id"]) || $authorID==$_SESSION["id"])
                echo "<a href=deleteComment.php?tweet=".$_GET["tweet"]."&id=$id style=float:right >Delete</a>";
            echo "<p>$comment</p>";
        }
        foot();
        ob_end_flush();
        ?>
    </main>
</body>
</html>