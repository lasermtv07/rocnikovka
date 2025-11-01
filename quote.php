<?php
session_start();
require 'comm.php';
$conn=connect();

if(isset($_GET["getCount"]) && isset($_GET["tweet"])){ //ziskej pocet quotu: pro API
    $stmt=$conn->prepare('SELECT COUNT(*) FROM tweets WHERE quote=?');
    $stmt->bind_param('i',$_GET["tweet"]);
    $stmt->execute();
    $count=$stmt->get_result()->fetch_assoc();
    echo $count['COUNT(*)'];
    die();
}

if(!isset($_SESSION["id"]) || !isset($_GET["tweet"])){
    die();
}

//zkontroluj jestli quotovanej tweet existuje
$stmt=$conn->prepare("SELECT COUNT(*) FROM tweets WHERE id=?");
$stmt->bind_param("i",$_GET["tweet"]);
$stmt->execute();
$count=($stmt->get_result())->fetch_assoc()["COUNT(*)"];

//switchne, za je tweet quotovanej
if($count>0){
    $test=$conn->query("SELECT COUNT(*) FROM tweets WHERE quote=".$_GET["tweet"]." AND authorID=".$_SESSION["id"]);
    $count=$test->fetch_assoc()["COUNT(*)"];
    if($count==0) //pridej quote
        $conn->query("INSERT INTO tweets (authorID,quote) VALUES (".$_SESSION['id'].",".$_GET["tweet"].")");
    else //smaz quote
        $conn->query("DELETE FROM tweets WHERE authorID=".$_SESSION['id']." AND quote=".$_GET["tweet"]);
}
else
    echo "error";
?>