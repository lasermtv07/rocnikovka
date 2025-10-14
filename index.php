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
            header('location:'.explode("/?",$_SERVER['REQUEST_URI'])[0]);
        }

    ?>
    <main>
        <h1>home</h1>
        <b><?php echo $_SESSION['nick']; ?></b>
        <form method=POST enctype="multipart/form-data">
            <textarea name=tweet style=width:99%;height:100px; ></textarea><br>
            <input type=submit name=s /> <input type=file name="image" />
            <?php 
                if(isAdmin($_SESSION['id'])){
                    echo "<a href=swears.php >Change swears</a>";
                }
            ?>
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
            if(mb_strlen($tweet)>255){
                echo "<b>Error: post cannot be longer than 256 characters!</b>";
                $cont=false;
            }
            $stmt=$conn->query('SELECT string FROM swears');
            while($i=$stmt->fetch_assoc()){
                if(!$cont)
                    continue;
                //TODO: zlepsit validaci
                if(preg_match('/'.$i['string'].'/i',$tweet)){
                    echo "<b>Error: cannot contain swears.</b>";
                    $cont=false;
                }
            }

            $file=$_FILES["image"];
            $fname="";

            //chyba uploadu (napr. config serveru..)
            if($file["tmp_name"]==""){
                echo "<b>Error: couldn't upload image</b>";
                $cont=false;
            }
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
                    echo "<b>Error: invalid uploaded file!</b>";
                    $cont=false;
                }
                elseif($file["size"]>500000){ //chzba kdyz moc velkej
                    echo "<b>Error: file too large</b>";
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
                $stmt=$conn->prepare('INSERT INTO `tweets` (authorID,`text`,picture,`postTime`) VALUES (?,?,?,CURRENT_TIMESTAMP)');
                $stmt->bind_param("iss",$_SESSION["id"],$tweet,$fname);
                $stmt->execute();

                header('location: .');
            }
        }
        //vypis existujici posty
        listTweets("");

        foot();
    ?>
    </main>
</body>
</html>