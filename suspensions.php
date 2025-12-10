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
        #statWrap {
            display:flex;
            justify-content: space-between;
        }
        .stat {
            display:inline-block;
        }
    </style>

</head>
<body>
    <?php
    head();
    $conn=connect();
    ?>
    <main>
    <h1>suspensions</h1>
    <p><b>Suspension</b> forbids the user from signing back in. Posts are still saved.<br>
    <b>Deletion</b> permanently removes the account and any activities associated with it, including tweets, likes and follows.</p>
    <hr />
    <h2>statistics</h2>
    <div id=statWrap >
    <?php
    function printAsTable($desc,$val){
        echo "<table class=stat><thead><th>$desc</th></thead>";
        echo "<tr><td>$val</td></tr></table>";
    }
    $tweetsTotal=$conn->query('SELECT COUNT(text) FROM tweets')->fetch_assoc()['COUNT(text)'];
    $accountsTotal=$conn->query('SELECT COUNT(*) FROM accounts')->fetch_assoc()['COUNT(*)'];
    $accountsSuspended=$conn->query('SELECT COUNT(*) FROM accounts WHERE suspension=1')->fetch_assoc()['COUNT(*)'];
    $mostFollowedId=$conn->query('SELECT  followedID,   COUNT(followedID) AS `value_occurrence`   FROM   follows  GROUP BY followedID  ORDER BY    `value_occurrence` DESC  LIMIT 1;')->fetch_assoc()['followedID'];
    $mostFollowed=$conn->query("SELECT username from accounts where id=$mostFollowedId")->fetch_assoc()['username'];

    printAsTable("Total tweets",$tweetsTotal);
    printAsTable("Total accounts",$accountsTotal);
    printAsTable("Suspended accounts",$accountsSuspended);
    printAsTable("Most followed",$mostFollowed);
    ?>
    </div>
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