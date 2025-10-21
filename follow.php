<?php
$id=$_GET["id"];

//checkni jestli user pihlasenej
session_start();
if(!isset($_SESSION["id"])){
    header('location:'.explode("follow.php",$_SERVER['REQUEST_URI'])[0]."profile.php?user=$id");
    die();
}

include 'comm.php';
$conn=connect();

//zjisti jestli uzivatel uz dal follow nebo ne
$stmt=$conn->prepare('SELECT * FROM follows WHERE followerID=? AND followedID=?');
$stmt->bind_param("ii",$_SESSION["id"],$id);
$stmt->execute();
$res=$stmt->get_result();
$res=mysqli_num_rows($res);

//handluj.. predchozi
if($res==0)
    $stmt=$conn->prepare('INSERT INTO follows VALUES (?,?)');
else
    $stmt=$conn->prepare('DELETE FROM follows WHERE followerID=? AND followedID=?');
$stmt->bind_param("ii",$_SESSION["id"],$id);
$stmt->execute();
echo $res;

header('location:'.explode("follow.php",$_SERVER['REQUEST_URI'])[0]."profile.php?user=$id");
?>