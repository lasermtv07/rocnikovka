<!DOCTYPE HTML>
<html>
    <head>
        <meta charset=UTF-8 />
        <title>register</title>
    </head>
    <body>
        <?php 
            require 'comm.php';
            head();
            $nick="";
            $pass="";
            $mail="";
            $bdate="";
            $option="";
            if(isset($_POST["s"])){
                $nick=$_POST["nick"];
                $pass=$_POST["pass"];
                $mail=$_POST["email"];
                $bdate=$_POST["bdate"];
                $option=$_POST["gender"];
            }
        ?>
        <h1>register</h1>
        <form method=POST>
            <table>
                <tr><td><b>Nickname: </b></td><td> <input type=text name=nick value="<?php echo $nick; ?>"/> </td></tr>
                <tr><td><b>Password: </b> </td><td><input type=password name=pass  value="<?php echo $pass; ?>"/> </td></tr>
                <tr><td><b>Email: </b></td><td> <input type=text name=email  value="<?php echo ($mail!="")?$mail:""; ?>" /> </td></tr>
                <tr><td><b>Date of birth:</td><td> </b> <input type=date name=bdate  value="<?php echo $bdate; ?>" /> </td></tr>
                <tr><td><b>Gender: </b></td><td>
                <select name="gender">
                    <option>  </option>
                    <option <?php echo ($option=="Male")?"selected":""; ?>>Male</option>
                    <option <?php echo ($option=="Female")?"selected":""; ?>>Female</option>
                    <option <?php echo ($option=="Other")?"selected":""; ?>>Other</option>
                </select>
                 </td></tr>
                <tr><td><input type=submit name=s ></td></tr>
            </table>
        </form>
<?php 
    if(isset($_POST["s"])){
        $nick=$_POST['nick'];
        $pass=$_POST['pass'];
        $email=$_POST['email'];
        $bdate=$_POST['bdate'];
        $gender=$_POST['gender'];

        //pripoj mysql
        $conn = connect();

        //validuj uziv. jm.
        if(!preg_match("/^[A-Za-z0-9ÁČĎÉĚÍŇÓŘŠŤÚŮÝŽáčďéěíňóřšťúůýž ]+$/",$nick) || mb_strlen($nick)<3){
            echo "<b>Error: illegal username</b>";
            foot();
            die();
        }
        //TODO: fix
        $users=$conn->query('SELECT * FROM `accounts` WHERE `username` = \''.$nick.'\'');
        if(mysqli_num_rows($users)!=0){
            echo "<b>Error: User already exists!</b>";
            foot();
            die();
        }
        //validuj heslo
        if($pass==="" || !preg_match("/^[A-Za-z0-9ÁČĎÉĚÍŇÓŘŠŤÚŮÝŽáčďéěíňóřšťúůýž.!]+$/",$pass) || mb_strlen($pass)<5) {
            echo "<b>Error: password must contain only alphanumeric characters nad must be atleast 5 characters long</b>";
            foot();
            die();
        }
        
        //validuj mail
        if($pass==="" || !preg_match("/[a-zA-Z0-9]+\@[a-zA-Z0-9]+\.[a-zA-Z0-9]+/",$email)){
            echo "<b>Error: wrong email format!</b>";
            foot();
            die();
        }

        //validuj narozeniny
        if($bdate==""){
            echo "<b>Error: must enter birthdate</b>";
            foot();
            die();
        }
        if(!preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/",$bdate)){
            echo "<b>Error: malformed birthdate</b>";
            foot();
            die();
        }

        $byear=(int)explode("-",$bdate)[0];
        $now=(int)date('Y',time());
        if($now-$byear<13){ //TODO: make more accurate
            echo "<b>To comply with applicable legislation, you must be atleast 13 y.o to register</b>";
            foot();
            die();
        }

        //validuj pohlavi
        if(!($gender=='Male' || $gender=='Female' || $gender=='Other')){
            echo "<b>Must enter a valid gender!</b>";
            foot();
            die();
        }

        //zkontroluj jestli uzivatel uz neni prihlasenej
        if(isset($_SESSION["id"]) && $_SESSION["id"]!=""){
            echo "<b>Error: already logged in as user!</b>";
            foot();
            die();
        }
        
        
        $nick=htmlspecialchars($nick);
        $pass=hash('sha256',$pass);
        $gender=mb_strtolower($gender);

        $stmt=$conn->prepare("INSERT INTO `accounts` (`username`, `password`,`date_of_birth`,`picture`,`description`,`banner`,`email`,`gender`) VALUES (?,?,?,'','','',?,?)");
        $stmt->bind_param("sssss",$nick,$pass,$bdate,$email,$gender);
        $stmt->execute();

        header('location: login.php');
    }

        foot();
?>
    </body>
</html>
