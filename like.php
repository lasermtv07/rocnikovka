<?php
require 'comm.php';
function ret($input){
    //dlouhej a kompliko0vanej zpusob jak generovat navr. adresy
    //vpodstate, vezmi vsechno pred like.php a appendni cil
    //je to tu proto, ze kdyz to bylo delany7 jinak, tak to na sk. serveru presmerovavalo spatne
    if($input=="")
        header('location:'.explode("like.php",$_SERVER['REQUEST_URI'])[0]);
    else
        header('location:'.explode("like.php",$_SERVER['REQUEST_URI'])[0]."profile.php?user=$input");
}


$conn=connect();

//API pro ziskani liku
if(isset($_GET["likecount"])){
    $likeCount=mysqli_num_rows($conn->query("SELECT * FROM likes WHERE tweetID=".$_GET["id"]));
    echo $likeCount;
    die();
}


var_dump($_GET);
$postID=$_GET["id"];
$returnPage=$_GET["ret"];
session_start();

$userID=$_SESSION["id"];
var_dump( $userID);
//die();
if(!isset($_SESSION["id"]) || $userID==NULL || $userID==""){
    ret($returnPage);
    die();
}


//zjisti, jestli uz like u tweetu je nebo ne
$stmt=$conn->prepare('SELECT * FROM likes WHERE userID=? AND tweetID=?');
$stmt->bind_param("ii",$userID,$postID);
$stmt->execute();
$out=$stmt->get_result();

$numRows=mysqli_num_rows($out);

if($numRows==0)
    $stmt=$conn->prepare("INSERT INTO likes VALUES (?,?)");
else
    $stmt=$conn->prepare("DELETE FROM likes WHERE userID=? AND tweetID=?");
$stmt->bind_param("ii",$userID,$postID);
$stmt->execute();

var_dump($out);

ret($returnPage);


?>