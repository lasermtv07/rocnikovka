<?php 
    require 'comm.php';
    $conn=connect();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/profile.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>profile</title>
</head>
<body>
    <?php 
   head(); 
    ?>

    <main>
<?php 
    $user=$_GET["user"];

    //nacti data
    $stmt=$conn->prepare("SELECT * FROM `accounts` WHERE `id` = ?");
    $stmt->bind_param("i",$user);
    $stmt->execute();
    $results=$stmt->get_result();
    $r=$results->fetch_assoc();
    
    $nick="anon";
    $picture="pfp/default.png";
    $description="";
    $banner="#b4b4b4ff";
    $textColor='black';

    if($r!=NULL){
        $nick=$r["username"];
        if($r['picture']!="")
            $picture=$r['picture'];
        $description=$r['description'];
        if($r['banner']!=NULL && $r['banner']!="")
            $banner=$r['banner'];
        if($r['textColor']!=NULL && $r['textColor']!="")
            $textColor=$r['textColor'];
    }
    echo "<style>#banner {background-color: $banner;";
    echo "color: $textColor;</style>";
?>

        <img id=pfp src="<?php echo $picture?>" alt="pfp" width="90" height="90"/>

    <?php echo "<h1>$nick</h1>";?>
        <?php 
        //spocitej followery
            $conn=connect();
            $stmt=$conn->prepare('SELECT count(*) FROM follows WHERE followedID=?');
            $stmt->bind_param("i",$user);
            $stmt->execute();
            $stmt=$stmt->get_result();
            $stmt=$stmt->fetch_assoc();
        if($user==$_SESSION["id"])
            echo "<span class=lr> <a href=\"profileConfig.php\" style=color:$textColor>profile</a> <a>follows: ";
        else 
            echo "<span class=lr> <a href=\"follow.php?id=".$_GET['user']."\" style=color:$textColor>follow";

            echo "(".$stmt["count(*)"].")</a></span><br><br>";
        ?>
    <p><?php echo $description; ?></p>
    <hr /><hr />
    <?php
        listTweets($user);
        //foot();
    ?>
    </main>
</body>
</html>