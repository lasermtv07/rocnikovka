<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>kod.php</title>
</head>
<body>
    <?php 
        foreach(glob(__DIR__.'/*',GLOB_BRACE) as $i){
            $str=file_get_contents($i);
            if(!preg_match("/secrets.php/",$i)){
                echo "<b>$i</b><br>";
                echo "<pre><code>".htmlspecialchars($str)."</code></pre>";
                echo "<hr>";
            }
        }
    ?>
</body>
</html>