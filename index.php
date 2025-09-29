<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
        require 'comm.php';
        head();
        session_start();
        if(isset($_GET["logout"])){
            session_destroy();
            header('location: .');
        }

    ?>
   <h1>home</h1>
    <form method=POST>
        <textarea name=tweet style=width:99%;height:100px; ></textarea><br>
        <input type=submit name=s />
    </form>
    <?php 


        $conn=connect();
        if(isset($_POST["s"])){

            $cont=true;
            if(!isset($_SESSION["nick"])){
                echo "<b>Error: must be logged in</b>";
                $cont=false;
            }
            //validuj tweet
            $tweet=$_POST["tweet"];
            if($tweet=="" && $cont){
                echo "<b>Error: post cannot be empty!</b>";
                $cont=false;
            }
            if(mb_strlen($tweet)>255 && $cont){
                echo "<b>Error: post cannot be longer than 256 characters!</b>";
                $cont=false;
            }
            //posli post
            $tweet=htmlspecialchars($tweet);
            if($cont){
                $stmt=$conn->prepare('INSERT INTO `tweets` (authorID,`text`,`postTime`) VALUES (?,?,CURRENT_TIMESTAMP)');
                $stmt->bind_param("is",$_SESSION["id"],$tweet);
                $stmt->execute();

                header('location: .');
            }
        }
        //vypis existujici posty
        listTweets("");

        foot();
    ?>
</body>
</html>