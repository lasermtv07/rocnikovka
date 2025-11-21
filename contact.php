<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <title>contact :: kotori</title>
    <style>
    #map{
        width: 50%;
        height: 200px;
        float:right;
    }
    #foot {
        margin-top:250px;
    }
    #data {
        float:left;
    }
    @media only screen and (max-width: 900px) {
        #map{ width:100%};
    }
    </style>
</head>
<body>
<?php 
require 'comm.php';
head();
?>
<main>
<h1>contact</h1>
<div id=data>
    <p>Author: <b>Michal Chmelař</b></p>
    <p>Definitely Real Social Media Company, LLC<br>
    Jeremenkova 69<br>
    779 00 Olomouc</p>

</div>
<div id=map></div>
<?php foot(); ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
let mapOptions = {
    center:[49.59, 17.27],
    zoom:10
}


let map = new L.map('map' , mapOptions);

let layer = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
map.addLayer(layer);

let marker = new L.Marker([49.59, 17.27]);
marker.addTo(map);
</script>
</main>
</body>
</html>