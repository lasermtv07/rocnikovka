<?php
require 'comm.php';
session_start();
$conn=connect();

//vyhod neadminy
if(!isset($_SESSION["id"]))
    header('location: index.php');

$stmt=$conn->prepare("SELECT authorID FROM tweets WHERE id = ?");
$stmt->bind_param("i",$_GET["id"]);
$stmt->execute();
$users=$stmt->get_result();
$id=$users->fetch_assoc()["authorID"];

if(!isAdmin($_SESSION["id"]) && $_SESSION["id"]!=$id){
    header('location: index.php');
    var_dump(isAdmin($_SESSION["id"]));
    die();
}


if(!isset($_GET["id"]))
    header('location: index.php');


try {
    $conn->query('DELETE FROM tweets WHERE id = '.$_GET["id"]);
} catch (Exception $e){
    echo "error";
}


    header('location: index.php');
?>