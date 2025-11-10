<?php
require 'comm.php';
session_start();

//validace 1
if(!isset($_GET["tweet"]))
    die();
if(!isset($_SESSION["id"]) || !isset($_GET["id"])){
    header("location:comments.php?tweet=".$_GET["tweet"]);
    die();
}
$tweet=$_GET["tweet"];
$id=$_GET["id"];
//valdidacae 2
if(!preg_match("/[0-9]+/",$tweet) || !preg_match("/[0-9]+/",$id)){
    header("location:comments.php?tweet=".$_GET["tweet"]);
    die();
}
$conn=connect();
$author=$conn->query("SELECT authorID FROM comments WHERE id=$id")->fetch_assoc()["authorID"];

if(isAdmin($_SESSION["id"]) || $_SESSION["id"]==$author){
    $conn->query("DELETE FROM comments WHERE id=$id");
}

header("location:comments.php?tweet=".$_GET["tweet"]);
?>