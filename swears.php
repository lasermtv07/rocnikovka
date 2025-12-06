<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>swears :: kotori</title>
    <?php 
    require 'comm.php';
    favicon(); 
    ?>
</head>
<body>
    <?php 
        head();
    ?>
    <main>
        <h1>swears</h1>
        <ul>
    <?php
if(!isAdmin($_SESSION['id'])){
    echo "<b>Error: must be admin!</b>";
    foot();
    die();
}
$comm=connect();
$stmt=$comm->query('SELECT string FROM swears');

//nacti sprostarny
for($i=$stmt->fetch_assoc();$i!=NULL;$i=$stmt->fetch_assoc()){
    echo "<li>".$i['string'];
    echo "<form method=POST style=display:inline ><input type=hidden name=target value=\"".$i['string']."\" /> <input type=submit name=s2 value=remove /></form></li>";
}

//pridej sprostarnu
if(isset($_POST["s"]) && isset($_POST["swear"]) && $_POST["swear"]!=""){
    $swear=htmlspecialchars($_POST["swear"]);
    $comm->query("INSERT INTO swears (string) VALUES ('$swear')");
    header('location:'.explode("/?",$_SERVER['REQUEST_URI'])[0]);
}

//odstran sprostarnu
if(isset($_POST['s2'])){
    $comm->query("DELETE from swears WHERE string='".$_POST['target']."'");
    header('location:'.explode("/?",$_SERVER['REQUEST_URI'])[0]);
}

    ?>
    <li><form method=POST ><input type=text name="swear" /> <input type="submit" name="s" value="add" /></form></li>
</ul>
<?php 

    foot();
?>
    </main>
</body>
</html>