<!DOCTYPE HTML>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
</head>
<body>
        <?php 
            require 'comm.php';
            head();
        ?>
    <h1>login</h1>
    <form method=POST>
        <table>
            <tr>
                <td><b>Nickname: </b></td>
                <td><input type=text name=nick /></td>
            </tr>
            <tr>
                <td><b>Password: </b></td>
                <td><input type=password name=pass /></td>
            </tr>
            <tr><td>
                <input type="submit" name="s" />
            </tr></td>
        </table>
    </form>

    <?php
        if(isset($_POST["s"])){
            if(!(isset($_POST["nick"]) && isset($_POST["pass"]))){
                echo "<b>Error: nickname/password not entered</b>";
                die();
            }
            $nick=$_POST["nick"];
            $pass=$_POST["pass"];

            if($nick=="" || $pass==""){
                echo "<b>Error: must enter nickname/password!</b>";
                die();
            }

            //pripoj mysql
            $conn = new mysqli('localhost', 'misa', '');
            if ($conn->connect_error){
                echo "<b>Database error!</b>";
                die();
            }
            try {
                $conn->query('use chmelarmi22');
            }
            catch(e) {
                echo "<b>Error loading database</b>";
                die();
            }

            //escapuj vstupy
            $nick=addslashes(htmlspecialchars($nick));
            $hashedPass=hash('sha256',$pass);

            //zkontroluj uzivatele
            $stmt=$conn->prepare("SELECT * FROM `accounts` WHERE `username` = ? AND `password` = ?");
            $stmt->bind_param("ss",$nick,$hashedPass);
            $stmt->execute();
            $users=$stmt->get_result();
            if(mysqli_num_rows($users)==0){
                echo "<b>Error: wrong username/password</b>";
                die();
            }
            session_start();
            $_SESSION["nick"]=$nick;
            header('location: .');

        }
    ?>
</body>
</html>