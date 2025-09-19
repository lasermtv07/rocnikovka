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
        ?>
        <h1>register</h1>
        <form method=POST>
            <table>
                <tr><td><b>Nickname: </b></td><td> <input type=text name=nick /> </td></tr>
                <tr><td><b>Password: </b> </td><td><input type=password name=pass /> </td></tr>
                <tr><td><b>Email: </b></td><td> <input type=text value="@" name=email /> </td></tr>
                <tr><td><b>Date of birth:</td><td> </b> <input type=date name=bdate /> </td></tr>
                <tr><td><b>Gender: </b></td><td>
                <select name="gender">
                    <option>  </option>
                    <option>Male</option>
                    <option>Female</option>
                    <option>Other</option>
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
            die();
        }
        //TODO: fix
        $users=$conn->query('SELECT * FROM `accounts` WHERE `username` = \''.$nick.'\'');
        if(mysqli_num_rows($users)!=0){
            echo "<b>Error: User already exists!</b>";
            die();
        }
        //validuj heslo
        if($pass==="" || !preg_match("/^[A-Za-z0-9ÁČĎÉĚÍŇÓŘŠŤÚŮÝŽáčďéěíňóřšťúůýž.!]+$/",$pass) || mb_strlen($pass)<5) {
            echo "<b>Error: password must contain only alphanumeric characters nad must be atleast 5 characters long</b>";
            die();
        }
        
        //validuj mail
        if($pass==="" || !preg_match("/[a-zA-Z0-9]+\@[a-zA-Z0-9]+\.[a-zA-Z0-9]+/",$email)){
            echo "<b>Error: wrong email format!</b>";
            die();
        }

        //validuj narozeniny
        if($bdate==""){
            echo "<b>Error: must enter birthdate</b>";
            die();
        }
        if(!preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/",$bdate)){
            echo "<b>Error: malformed birthdate</b>";
            die();
        }

        $byear=(int)explode("-",$bdate)[0];
        $now=(int)date('Y',time());
        if($now-$byear<13){ //TODO: make more accurate
            echo "<b>To comply with applicable legislation, you must be atleast 13 y.o to register</b>";
            die();
        }

        //validuj pohlavi
        if(!($gender=='Male' || $gender=='Female' || $gender=='Other')){
            echo "<b>Must enter a valid gender!</b>";
            die();
        }
        
        
        $nick=htmlspecialchars($nick);
        $pass=hash('sha256',$pass);
        $gender=mb_strtolower($gender);

        $stmt=$conn->prepare("INSERT INTO `accounts` (`username`, `password`,`date_of_birth`,`picture`,`description`,`banner`,`email`,`gender`) VALUES (?,?,?,'','','',?,?)");
        $stmt->bind_param("sssss",$nick,$pass,$bdate,$email,$gender);
        $stmt->execute();


        //header('location: '.$_SERVER['SCRIPT_NAME']);
    }
?>
    </body>
</html>
