<?php
require 'comm.php';
session_start();
$conn=connect();

//vyhod neadminy
if(!isset($_SESSION["id"]))
    header('location: index.php');

$stmt=$conn->prepare("SELECT * FROM tweets WHERE id = ?");
$stmt->bind_param("i",$_GET["id"]);
$stmt->execute();
$users=$stmt->get_result();
$assoc=$users->fetch_assoc();
$id=$assoc["authorID"];
$pic=$assoc["picture"];

var_dump($pic);

if(!isAdmin($_SESSION["id"]) && $_SESSION["id"]!=$id){
    header('location: index.php');
    var_dump(isAdmin($_SESSION["id"]));
    die();
}


if(!isset($_GET["id"]))
    header('location: index.php');

try {
    $conn->query('DELETE FROM tweets WHERE id = '.$_GET["id"]);
    unlink('images/'.$pic);
} catch (Exception $e){
    echo "error";
}

$conn->query("DELETE FROM likes WHERE tweetID=".$_GET["id"]);
$conn->query("DELETE FROM tweets WHERE quote=".$_GET["id"]);

    header('location: index.php');
?>