<!DOCTYPE HTML>
<html>
    <head>
        <meta charset=UTF-8 />
        <title>register :: kotori</title>
        <style>
            .warn {
                font-weight: bold;
                color: var(--light-red)
            }
        </style>
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
                $passConf=$_POST['passConf'];
                $mail=$_POST["email"];
                $bdate=$_POST["bdate"];
                $option=$_POST["gender"];
            }
        ?>
        <main>
        <h1>register</h1>
        <form method=POST>
            <table>
                <tr><td><b>Nickname: </b><span id=labelNick></span></td><td> <input type=text name=nick id=nick value="<?php echo $nick; ?>"/><span id=validNick class=warn></span></td></tr>
                <tr><td><b>Password: </b> <span id=labelPass></span></td><td><input type=password name=pass id=pass value="<?php echo $pass; ?>"/> <span id=validPass class=warn></span></td></tr>
                <tr><td><b>Confirm password: </b> <span id=labelConf></span></td><td><input type=password name=passConf id=conf value="<?php echo $passConf; ?>"/><span id=validConf class=warn></span></td></tr>
                <tr><td><b>Email: </b><span id=labelEmail></span></td><td> <input type=text name=email id=email value="<?php echo ($mail!="")?$mail:""; ?>" /> <span id=validEmail class=warn></span></td></tr>
                <tr><td><b>Date of birth:</td><td> </b> <input type=date name=bdate id=bdate value="<?php echo $bdate; ?>" /> </td></tr>
                <tr><td><b>Gender: <span id=labelGender></span></b></td><td>
                <select name="gender" id=gender>
                    <option>  </option>
                    <option <?php echo ($option=="Male")?"selected":""; ?>>Male</option>
                    <option <?php echo ($option=="Female")?"selected":""; ?>>Female</option>
                    <option <?php echo ($option=="Other")?"selected":""; ?>>Other</option>
                </select>
                <span id=validGender class=warn></span>
                 </td></tr>
            </table>
            <input type=checkbox name=legal id=legal>
            <label for=legal ><b>By registering, I agree to obey <a href=rules.php >the rules of the platform</a>.</b></label>
            <br>
            <input type=submit name=s value="Register" >
        </form>
        <script src=js/validation.js ></script>
<?php 
    if(isset($_POST["s"])){

        $email=$mail;
        $gender=$option;
        //pripoj mysql
        $conn = connect();

        //validuj souhlas s podminkama
        if(!isset($_POST["legal"])){
            errorBox("<b>Error: you must agree to the rules</b>");
            foot();
            die();
        }
        //validuj uziv. jm.
        if(!preg_match("/^[A-Za-z0-9ÁČĎÉĚÍŇÓŘŠŤÚŮÝŽáčďéěíňóřšťúůýž -]+$/",$nick) || mb_strlen($nick)<3){
            errorBox("<b>Error: illegal username</b>");
            foot();
            die();
        }
        //TODO: fix
        $users=$conn->query('SELECT * FROM `accounts` WHERE `username` = \''.$nick.'\'');
        if(mysqli_num_rows($users)!=0){
            errorBox("<b>Error: User already exists!</b>");
            foot();
            die();
        }
        //validuj heslo
        if($pass==="" || mb_strlen($pass)<5) {
            errorBox("<b>Error: password must be atleast 5 characters long</b>");
            foot();
            die();
        }
        //konfirmace hesla
        if($pass!=$passConf){
            errorBox("Error: confirmation password doesn't match!");
            foot();
            die();
        }
        
        //validuj mail
        if($email==="" || !preg_match("/[a-zA-Z0-9]+\@[a-zA-Z0-9]+\.[a-zA-Z0-9]+/",$email)){
            errorBox("<b>Error: wrong email format!</b>");
            foot();
            die();
        }

        //validuj narozeniny
        if($bdate==""){
            errorBox("<b>Error: must enter birthdate</b>");
            foot();
            die();
        }
        if(!preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/",$bdate)){
            errorBox("<b>Error: malformed birthdate</b>");
            foot();
            die();
        }

        $byear=(int)explode("-",$bdate)[0];
        $now=(int)date('Y',time());
        if($now-$byear<13){ //TODO: make more accurate
            errorBox("<b>To comply with applicable legislation, you must be atleast 13 y.o to register</b>");
            foot();
            die();
        }

        //validuj pohlavi
        if(!($gender=='Male' || $gender=='Female' || $gender=='Other')){
            errorBox("<b>Must enter a valid gender!</b>");
            foot();
            die();
        }

        //zkontroluj jestli uzivatel uz neni prihlasenej
        if(isset($_SESSION["id"]) && $_SESSION["id"]!=""){
            errorBox("<b>Error: already logged in as user!</b>");
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
    </main>
    </body>
</html>
