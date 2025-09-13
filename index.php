<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
        require 'comm.php';
        head();
        session_start();
        if(isset($_GET["logout"])){
            session_destroy();
            header('location: .');
        }

    ?>
    <h1>home</h1>
</body>
</html>