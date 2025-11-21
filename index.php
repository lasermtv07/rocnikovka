<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>kotori</title>
    <style>
        #feed h3{
            width: 100%;
            display: flex;
            justify-content: space-evenly;
        }
        #feed a {
            text-decoration: none;
        }
        #feed .discoverH {
            text-decoration: underline;
        }

    </style>
</head>
<body>
    <?php
        require 'comm.php';
        head();
        session_start();
        if(isset($_GET["logout"])){
            session_destroy();
            header('location:'.explode("/?",$_SERVER['REQUEST_URI'])[0]);
        }

        if(isset($_GET["follows"])){
            setcookie('follows','1',time()+3600*24*30);
        }
        if(isset($_GET["disc"])){
            setcookie('follows','',0);
            unset($_COOKIE["follows"]);
        }
        $tags=['Sports','News','Gaming','Tech','Science','Movies','Music'];
    ?>
    <div id=right>
        aaa
    </div>
    <main>

        <div id=feed >
            <h3>
                <?php
                if(!(isset($_COOKIE["follows"])|| isset($_GET["follows"])))
                    echo "<b class=discoverH>";
                ?>
                <a href=index.php?disc=1 >Discover</a>
                <?php
                if(!(isset($_COOKIE["follows"])|| isset($_GET["follows"])))
                    echo "</b>";
                if((isset($_COOKIE["follows"])|| isset($_GET["follows"])))
                    echo "<b class=discoverH>";
                ?>
                <a href="index.php?follows=1" >Following</a>
                <?php
                if((isset($_COOKIE["follows"])|| isset($_GET["follows"])))
                    echo "</b>";
                ?>
            </h3>
        </div>
        <b><?php echo $_SESSION['nick']; ?></b>
        <form method=POST enctype="multipart/form-data">
            <textarea name=tweet style=width:99%;height:100px; ></textarea><br>
            <input type=submit name=s value="Send" /> <input type=file name="image" /><br />
            <?php
                foreach($tags as $i){
                    echo "<input type=\"checkbox\" name=\"tag$i\" id=\"tag$i\"> <label for=\"tag$i\">$i</label><br>";
                }
            ?>
            <?php 
                if(isAdmin($_SESSION['id'])){
                    echo "<a href=swears.php >Change swears</a>";
                    echo " <a style=padding-left:50px href=suspensions.php >Suspend/delete users</a>";
                }
            ?>
        </form>
    <?php 

        $conn=connect();
        if(isset($_POST["s"])){

            $cont=true;
            if(!isset($_SESSION["nick"])){
                errorBox("Error: must be logged in");
                $cont=false;
            }
            //validuj tweet
            $tweet=$_POST["tweet"];
            if($tweet=="" && $cont){
                errorBox("<b>Error: post cannot be empty!</b>");
                $cont=false;
            }
            if(mb_strlen($tweet)>255){
                errorBox("<b>Error: post cannot be longer than 256 characters!</b>");
                $cont=false;
            }
            $stmt=$conn->query('select string from swears');
            while($i=$stmt->fetch_assoc()){
                if(!$cont)
                    continue;
                //todo: zlepsit validaci
                if(preg_match('/'.$i['string'].'/i',$tweet)){
                    errorBox( "<b>error: cannot contain swears.</b>");
                    $cont=false;
                }
            }

            $file=$_FILES["image"];
            $fname="";
/*
            //chyba uploadu (napr. config serveru..)
            var_dump($_FILES);
            if($file["tmp_name"]==""){
                echo "<b>Error: couldn't upload image</b>";
                $cont=false;
            }
*/
            if($file['name']!="" && $cont){
                //najdi nejvyse postaveny obrazek
                $maxNo=0;
                foreach(scandir('images') as $i){
                    if($i!='.' && $i!='..'){
                        $j=explode(".",$i)[0];
                        if((int)$j>$maxNo)
                            $maxNo=$j;
                    }
                }
                $maxNo++;

                $ext=explode(".",$_FILES["image"]["name"])[1]; //pripona
                $check=getimagesize($file["tmp_name"]);
                if(!$check){ //vyhodi chybu kdyz neni obrazek
                    errorBox("<b>Error: invalid uploaded file!</b>");
                    $cont=false;
                }
                elseif($file["size"]>500000){ //chzba kdyz moc velkej
                    errorBox("<b>Error: file too large</b>");
                    $cont=false;
                }
                else {
                    $fname=$maxNo.".".$ext;
                    move_uploaded_file($file["tmp_name"],"images/".$fname);
                }
            }

            //posli post
            $tweet=htmlspecialchars($tweet);
            if($cont){
                //preved tagy na string
                $tagString="";
                foreach($tags as $i){
                    if(isset($_POST["tag$i"]))
                        $tagString.=$i.";";
                }

                $stmt=$conn->prepare('INSERT INTO `tweets` (authorID,`text`,picture,`postTime`,`tags`) VALUES (?,?,?,CURRENT_TIMESTAMP,?)');
                $stmt->bind_param("isss",$_SESSION["id"],$tweet,$fname,$tagString);
                $stmt->execute();

                if(isset($_GET["follows"]) || isset($_COOKIE["follows"]))
                    header('location: .?follows=1');
                else
                    header('location: .');
            }
        }
        //vypis existujici posty
        if(isset($_COOKIE["follows"]) || isset($_GET["follows"]))
            listTweets($_SESSION["id"],true);
        else
            listTweets("");

        foot();
    ?>
    </main>
</body>
</html>