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
        $conn=connect();
        $id=$_SESSION["id"];

        $stmt=$conn->prepare("SELECT * FROM `accounts` WHERE `id` = ?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $result=$stmt->get_result();
        $r=$result->fetch_assoc();
        #var_dump($r);
        if(!isset($_SESSION["id"])){
            echo "<b>Error: must be logged in!</b>";
            die();
        }
    ?>
    <main>
    <h1>profile config</h1>
    <form method=POST >
        <b>Nickname: </b><input type="text" name="nick" value="<?php echo $r['username']; ?>"/>
        <br><b>Description: </b><br>
        <textarea name="desc" style=width:100%;height:100px;>
<?php echo $r["description"]; ?>
</textarea>

        <b>Banner color:</b><input type="color" name="banner" value="<?php echo $r["banner"]; ?>" /><br>
        <b>Title color:</b><input type="color" name="textColor" value="<?php echo $r["textColor"]; ?>" /><br>
        <input type="submit" name="s" />
    </form>
<?php 
if(isset($_POST["s"])){
    $nick=htmlspecialchars($_POST["nick"]);
    $desc=htmlspecialchars($_POST["desc"]);
    //TODO: add validation
    $banner=$_POST["banner"];
    $textColor=$_POST["textColor"];
    
    //validuj prazdnej nick
    if(strlen($nick)==0 || $nick==""){
        echo "<b>Error: nick cannot be empty!</b>";
        foot();
        die();
    }

    $stmt=$conn->prepare('UPDATE `accounts` SET `username` = ?, `description` = ?, `banner` = ?, `textColor`=? WHERE `id` = ?');
    $stmt->bind_param("ssssi",$nick,$desc,$banner,$textColor,$id);
    $stmt->execute();

    header('location:'.$_SERVER['REQUEST_URI']);
    $_SESSION['nick']=$nick;
    

}    
foot();
?>

</main>
</body>
</html>