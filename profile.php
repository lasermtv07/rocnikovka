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
    $picture="png/default.png";
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

    <p id=banner >
        <img id=pfp src="<?php echo $picture?>" alt="pfp" width="90" height="90"/>
        <span><?php echo $nick; ?></span>
        <?php 
        if($user==$_SESSION["id"])
            echo "<a href=\"profileConfig.php\" style=color:$textColor>profile</a>";
        ?>
</p>

    <p><?php echo $description; ?></p>
    <hr /><hr />
    <?php
        listTweets($user);
        //foot();
    ?>
    </main>
</body>
</html>