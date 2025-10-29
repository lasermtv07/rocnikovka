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
    <main>
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

            $conn=connect();
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
                foot();
                die();
            }
            if($users->fetch_assoc()["suspension"]==1){
                echo "<b>Error: account suspended</b>";
                foot();
                die();
            }
            session_start();

            //check login
            if(isset($_SESSION["id"]) && $_SESSION["id"]!=""){
                echo "<b>Error:already logged in!</b>";
                foot();
                die();
            }



            $_SESSION["nick"]=$nick;

            //nastav userId
            $stmt=$conn->prepare("SELECT `id` FROM `accounts` WHERE `username` = ?");
            $stmt->bind_param("s",$nick);
            $stmt->execute();
            $result=$stmt->get_result();
            $r=$result->fetch_assoc();
            $_SESSION["id"]=$r["id"];



            header('location: .');

        }
        foot();
    ?>
    </main>
</body>
</html>