<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>suspensions :: kotori</title>
    <?php 
    require 'comm.php';
    favicon(); 
    ?>
    <link rel=stylesheet href=css/style.css />
    <style>
        table {
            border-collapse:collapse;
            background-color:var(--bg);
        }
        table,td,th {
            border:2px solid black;
            border-color: var(--fg);
        }
        td,th {
            padding:2px 10px;
        }
    </style>

</head>
<body>
    <?php head();?>
    <main>
    <h1>suspensions</h1>
    <p><b>Suspension</b> forbids the user from signing back in. Posts are still saved.<br>
    <b>Deletion</b> permanently removes the account and any activities associated with it, including tweets, likes and follows.</p>
    <hr />
    <?php 
        if(!isAdmin($_SESSION["id"])){
            echo "<b>Error: must be logged in as admin</b>";
            die();
        }

        if(isset($_GET["suspend"])){
            suspend($_GET["suspend"]);
            header('location: '.explode("?",$_SERVER["REQUEST_URI"])[0]);
        }
        if(isset($_GET["delete"])){
            deleteAcc($_GET["delete"]);
            header('location: '.explode("?",$_SERVER["REQUEST_URI"])[0]);
        }

        $conn=connect();
        //listuj uzivatele
        echo "<table>";
        echo "<thead><th>a</th><th>Nick</th><th>Email</th><th>Birth Date</th><th>Gender</th><th>Suspend</th><th>Delete</th></thead>\n";
        $stmt=$conn->query("SELECT id,username,email,date_of_birth,gender,isAdmin,suspension FROM accounts");
        while($i=$stmt->fetch_assoc()){
            echo "<tr><td>".(($i["isAdmin"]==1)?"*":"&nbsp;")."</td>";
            echo "<td>".$i["username"]."</td><td>".$i["email"]."</td>";
            echo "<td>".$i["date_of_birth"]."</td><td>".$i["gender"]."</td>";
            //suspension - pozastav. uctu
            echo "<td><a href=\"".$_SERVER["REQUEST_URI"]."?suspend=".$i["id"]."\">";
            if($i["suspension"]==1)
                echo "Unsuspend</a>";
            else
                echo "Suspend</a>";
            //mazani
            echo "<td><a href=\"".$_SERVER["REQUEST_URI"]."?delete=".$i["id"]."\">Delete</a></td>";
            echo "</tr>\n";
        }
        echo "</table>";
        foot();
    ?>
    </main>
</body>
</html>