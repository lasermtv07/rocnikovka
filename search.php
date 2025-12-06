<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>search :: kotori</title>
    <?php 
    require 'comm.php';
    favicon(); 
    ?>
    <style>
        #search {
            width: calc( 100% - 75px );
        }
        #filter {
            width: 100%;
            display: flex;
            justify-content: space-evenly;
        }
        .highlight {
            font-weight: bold;
        }
        h3 a {
            text-decoration:none;
        }
        .description {
            overflow-wrap:anywhere;
        }
        .name {
            font-size: 1.2rem;
            font-weight:bold;
        }
    </style>
</head>
<body>
<?php
head();
$conn=connect();
?>
<main>
    <h1>search</h1>
    <form method=GET>
        <input type=text name=search id=search
        value="<?php echo $_GET["search"];?>"
        /> <input type=submit name=s value="Search" />
        <input type=hidden name="<?php echo isset($_GET["tweet"])?"tweet":"prof";?>" />
    </form>

    <h3><div id=filter>
<?php
//prepinac typu hledani
$baseUrl=parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$query=urlencode($_GET["search"]);
$text1="<a href=\"$baseUrl?search=$query\" >Profiles</a>";
$text2="<a id=postsBtn href=\"$baseUrl?search=$query&tweet\" >Posts</a>";
if(isset($_GET["tweet"])){
    echo "<span>$text1</span>";
    echo "<span><u><b>$text2</b></u></span>";
} else {
    echo "<span><u><b>$text1</b></u></span>";
    echo "<span>$text2</span>";
}

?>
</div></h3>
<hr class=delim >
<?php 
if(!isset($_GET["tweet"])){
    $stmt=$conn->prepare("SELECT id,username,picture,`description` FROM accounts WHERE username LIKE ? OR `description` LIKE ?");
    $query="%".htmlspecialchars($_GET["search"])."%";
    $stmt->bind_param("ss",$query,$query);
    $stmt->execute();
    $stmt=$stmt->get_result();

    while($i=$stmt->fetch_assoc()){
        //var_dump($i);
        $id=$i['id'];
        $nick=$i['username'];
        $picture=($i['picture']=="")?"pfp/default.png":$i['picture'];
        $description=mb_substr($i['description'],0,80);

        echo <<<EOF
        <table>
        <tr><td rowspan=2 ><img src=$picture width=70px height=70px /></td><td><span class=name><a href=profile.php?user=$id >$nick</a></span></td></tr>
        <tr><td class=description><span class=description>$description&nbsp;</span></td>
        </table>
        EOF;
        echo "<hr class=delim />";
    }
}
else {
    listTweets("",match:htmlspecialchars($_GET["search"]));
}
foot();
?>
<script>
//resetuje cookie
document.getElementById("postsBtn").onclick=()=>{
    let time=Date.now();
    document.cookie = `pageSearch=1; expires=${time}`;
}
</script>
</main>
</body>
</html>