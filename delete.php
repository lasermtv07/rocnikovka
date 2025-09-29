<?php
require 'comm.php';
session_start();

//vyhod neadminy
if(!isset($_SESSION["id"]))
    header('location: index.php');
if(!isAdmin($_SESSION["id"]))
    header('location: index.php');

if(!isset($_GET["id"]))
    header('location: index.php');

$conn=connect();
try {
    $conn->query('DELETE FROM tweets WHERE id = '.$_GET["id"]);
} catch (Exception $e){
    echo "error";
}


    header('location: index.php');
?>