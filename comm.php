<?php
    function connect(){
            require 'secrets.php';
            //pripoj mysql
            $conn = new mysqli('localhost', 'misa', $mysql_password);
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
            return $conn;
    }
    function isAdmin($id){
        try {
            $conn=connect();
            $r=$conn->query("SELECT isAdmin FROM accounts WHERE id = $id;");
            $r=$r->fetch_assoc();
            return ($r["isAdmin"])?true:false;
        } catch (Exception $e) {
            return false;
        }
    }

    function head(){
        session_start();
        echo '<link rel=stylesheet href=css/style.css />';
        echo "<div id=head>";
        echo '<center>';
        echo '<h1>小鳥</h1><span>';
        echo '<h2><a href="index.php">Home</a></h2> <h2><a href="login.php">Login</a></h2> <h2><a href="register.php">Register</a></h2>';

        echo "<h2><a href=\"index.php/?logout\" >Logout</a></h2>";
        echo '<h2>';
        if(in_array("?",str_split($_SERVER['REQUEST_URI'])))
            echo "<a href=".$_SERVER['REQUEST_URI']."&";
        else
            echo "<a href=".$_SERVER['REQUEST_URI']."?";

        if(isset($_COOKIE["light"]))
            echo "change>dark</a>";
        else
            echo "change>light</a>";
        echo '</h2>';
        $nick=(isset($_SESSION["nick"]))?$_SESSION["nick"]:"anon";
        echo "</span>";
        if(isset($_COOKIE["light"])){
            echo "<link rel=stylesheet href=\"css/light.css\" />";
        }
        if(isset($_GET["change"])){
            if(!isset($_COOKIE["light"])){
                setcookie("light","1",time()+3600*24*30);
            }
            else {
                setcookie("light",!$_COOKIE["light"],time()+3600*24*30);
            }
            $out_url=str_replace("?change","",$_SERVER['REQUEST_URI']);
            $out_url=str_replace("&change","",$out_url);
            header('location:'.$out_url);
        }
        echo "</center></div>";

        //odhlas suspended/zabanovane uzivatele
        if(isset($_SESSION["id"])){
            $conn=connect();
            $stmt=$conn->query("SELECT suspension FROM accounts WHERE id=".$_SESSION["id"]);
            if(mysqli_num_rows($stmt)==0)
                session_destroy();
            elseif($stmt->fetch_assoc()["suspension"]==1)
                session_destroy();
        }

    }

    function foot(){
        echo "<div id=foot><hr />(c) Michal Chmelar 2025. ";
        if(!isset($_COOKIE["visited"])){
            $t=file_get_contents('visits.txt');
            file_put_contents('visits.txt',(int)$t+1);
        }

        echo "<b>VISITED: ". file_get_contents('visits.txt');
        setcookie('visited',"true",time()+86400*30,"/");
        echo "</div>";
    }
    function listTweets($user,$changeFeed=false,$pagingLimit=10){
        $conn=connect();
        //cookie pro pagovani
        $pageId=(is_null($_GET["user"]))?"":$_GET["user"];
        if(!isset($_COOKIE["page".$pageId]))
            setcookie("page".$pageId,"1",time()+3600);

        $limitStart=max(($_COOKIE["page".$pageId]-1)*$pagingLimit,0);
        $limitString="LIMIT ".$limitStart.",".($limitStart+$pagingLimit);

        //paguj dopredu
        if(isset($_GET["pageNext"])){
            setcookie("page".$pageId,$_COOKIE["page".$pageId]+1,time()+3600);
            $newUrl=str_replace("?pageNext","",$_SERVER["REQUEST_URI"]);
            $newUrl=str_replace("&pageNext","",$newUrl);
            header('location: '.$newUrl);
        }
        //paguj dozadu
        if(isset($_GET["pagePrev"])){
            if($_COOKIE["page".$pageId]>1)
                setcookie("page".$pageId,$_COOKIE["page".$pageId]-1,time()+3600);
            $newUrl=str_replace("?pagePrev","",$_SERVER["REQUEST_URI"]);
            $newUrl=str_replace("&pagePrev","",$newUrl);
            header('location: '.$newUrl);
        }

        echo "<script src=js/like.js ></script>";
        echo "<br>";
        if(!$changeFeed || $user==NULL){
            if($user=="") //pokud generuje homepage (discover)
                $stmt=$conn->query("SELECT tweets.*,accounts.username FROM tweets INNER JOIN accounts ON tweets.authorID = accounts.id ORDER BY id DESC $limitString");
            else //pokud generuje stranku uzivatele
                $stmt=$conn->query("SELECT tweets.*,accounts.username FROM tweets INNER JOIN accounts ON tweets.authorID = accounts.id WHERE accounts.id = $user ORDER BY id DESC $limitString");
        } else { // homepage ale following feed
            $subStmt=$conn->query("SELECT followedID FROM follows WHERE followerID='$user' $limitString");
            $stmtText="($user";
            while($i=$subStmt->fetch_assoc()){
                $stmtText.=",";
                $stmtText.=$i["followedID"];
            }
            $stmtText.=")";
            $stmt=$conn->query("SELECT tweets.*,accounts.username FROM tweets INNER JOIN accounts ON tweets.authorID = accounts.id WHERE accounts.id IN $stmtText ORDER BY id DESC $limitString");
            
        }
        $pageCount=0;
        while($i = $stmt->fetch_assoc()){
            $pageCount++;
            session_start();
            $id=$i['id'];
            $authorID=$i['authorID'];
            $username=$i['username'];
            $text=$i['text'];
            $postTime=$i['postTime'];
            $picture=$i['picture'];
            $quote=$i['quote'];
            
            if($quote!=NULL){
                echo "<b>Reposted by: $username</b><br>";
                $st=$conn->query("SELECT tweets.*,accounts.username FROM tweets INNER JOIN accounts ON tweets.authorID = accounts.id WHERE tweets.id=$quote;");
                $st=$st->fetch_assoc();
                $id=$quote;
                $authorID=$st['authorID'];
                $username=$st['username'];
                $text=$st['text'];
                $postTime=$st['postTime'];
                $picture=$st['picture'];
                $quote=$st['quote'];
            }

            echo "<b><a href=profile.php?user=".$authorID." >".$username."</a></b> - ".$postTime;
            //mazani
            if(isAdmin($_SESSION["id"]) || $_SESSION["id"]==$authorID)
                echo "<a style=color:red;float:right href=delete.php?id=".$id.">Delete</a>";
            echo "<p>".$text."</p>";
            //obrazek
            if($picture)
                echo "<img src=images/".$picture." class=post_img />";
            //liky
            $likeCount=mysqli_num_rows($conn->query("SELECT * FROM likes WHERE tweetID=".$id));
            //barveni pro liky ktere udelil uzivatel
            if(isset($_SESSION["id"])){
                $r=$conn->query("SELECT * FROM likes WHERE userID=".$_SESSION["id"]." AND tweetID=".$id);
                $count=mysqli_num_rows($r);
            } else
                $count=0;
            $color="";
            if($count>0)
                $color=" liked";

            echo "<div class=buttons><span class=a><span style=font-size:2em;$color >";
            echo "<span id=\"lc".$id."\"onclick=addLike('like.php?id=".$id."&ret=$user',".$id.",".((isset($_SESSION["id"]))?'true':'false').") class=\"like$color\">♥ <span id=l".$id. " style=\"color:var(--fg) !important;font-size:1.25rem;\">$likeCount</span></span></span>";
            echo "</span>";
            //reposty (quoty)
            $st=$conn->query("SELECT COUNT(*) FROM tweets WHERE quote=".$id);
            $repostCount=$st->fetch_assoc()["COUNT(*)"];

            $green="";
            if(isset($_SESSION["id"])){
                $st=$conn->query("SELECT COUNT(*) FROM tweets WHERE quote=$id AND authorID=".$_SESSION["id"]);
                if($st->fetch_assoc()['COUNT(*)']!=0)
                    $green="green";
            }

            echo "<span class=b><span onclick=addRepost($id,".((isset($_SESSION["id"]))?1:0).") >";
            echo "<span id=rc$id class=\"$green\" >".file_get_contents('ico/repost.svg')."</span>";
            echo "<span id=r$id style=font-size:1.5em class=up >$repostCount</span></span></span>";
            echo "<span><a href=\"comments.php?tweet=".$id."\">comments</a></span>";
            echo "</div>";
            echo "<hr>";
        }

        //UI pro pager
        $linkNext=$_SERVER['REQUEST_URI'];
        if($pageCount>0){
            if(str_contains($linkNext,"?") && !str_contains($linkNext,"&pageNext"))
                $linkNext.="&pageNext";
            else if(!str_contains($linkNext,"&pageNext"))
                $linkNext.="?pageNext";
        }

        $linkPrev=$_SERVER['REQUEST_URI'];
        if(str_contains($linkPrev,"?") && !str_contains($linkPrev,"&pagePrev"))
            $linkPrev.="&pagePrev";
        else if(!str_contains($linkPrev,"&pagePrev"))
            $linkPrev.="?pagePrev";

        echo "<div id=pager>";
        echo "<h3><a href=\"$linkPrev\">&lt;</a></h3>";
        echo "<h3> ".((is_null($_COOKIE["page".$pageId]))?"1":$_COOKIE["page".$pageId])." </h3>";
        echo "<h3><a href=\"$linkNext\">&gt;</a></h3>";
        echo "</div>";
    }

    function suspend($id){
        //zkontroluj opravneni
        if(!isAdmin($_SESSION["id"]))
            return false;

        $conn=connect();
        //zkontroluj jestli obet neni admin a jestli je dobre id
            $stmt=$conn->query("SELECT isAdmin FROM accounts WHERE id=$id");
            if($stmt->fetch_assoc()["isAdmin"]==1)
                return false;

        $stmt=$conn->query("SELECT suspension FROM accounts WHERE id=$id");
        if($stmt->fetch_assoc()["suspension"]==1)
            $conn->query("UPDATE accounts SET suspension=0 WHERE id=$id");
        else
            $conn->query("UPDATE accounts SET suspension=1 WHERE id=$id");

    }
    //tady checky nedelam protoae tohle bude i ke smazani uctu uzivatelem samotnym
    function deleteAcc($id){
        $conn=connect();

        //smaz fotky
        $stmt=$conn->query("SELECT picture FROM tweets WHERE authorID=$id");
        while($i=$stmt->fetch_assoc()){
            if($i["picture"]!=NULL && $i["picture"]!='NULL')
                unlink('images/'.$i['picture']);
        }
        //smaz vsechny tweety
        $conn->query("DELETE FROM tweets WHERE authorID=$id");
        //smaz vsechny likes
        $conn->query("DELETE FROM likes WHERE userID=$id");
        //smaz i follow relace
        $conn->query("DELETE FROM follows WHERE followerID=$id or followedID=$id");
        //smaz ucet
        $conn->query("DELETE FROM accounts WHERE id=$id");

        //odhlas pokud prihlasenej
        if($_SESSION["id"]==$id)
            session_destroy();
    }

?>