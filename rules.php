<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>rules :: kotori</title>
    <style>
        textarea[name=legal]{
            height:70vh;
        }
    </style>
</head>
<body>
<?php 
require 'comm.php';
head();    
?>
<main>
    <h1>usage rules</h1>
    <?php
    if(isAdmin($_SESSION["id"]))
        echo "<form method=POST><textarea name=legal>";
    try {
        echo file_get_contents("rules.txt");
    } catch(e) {
        echo "File not found";
    }
    if(isAdmin($_SESSION["id"]))
        echo "</textarea><input type=submit name=s value=\"Change\" /></form>";
    ?>

    <?php 
if(isset($_POST["s"]) && isAdmin($_SESSION["id"])){
    file_put_contents("rules.txt",$_POST["legal"]);
    header('location:'.$_SERVER['REQUEST_URI']);
}
foot();
    ?>
    <script type="text/javascript" src="./js/tinymce/tinymce.min.js"></script>
	<script type="text/javascript">
		tinymce.init(
			{
				language : 'en',
				selector: 'textarea[name=legal]',
				theme: 'modern',
                content_css: 'writer',
				plugins: [
					'hr anchor pagebreak',
					'searchreplace wordcount visualblocks visualchars code',
					'insertdatetime nonbreaking save table directionality',
					'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc'
				],
				toolbar1: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
				toolbar2: 'print preview media | forecolor backcolor emoticons | codesample',
				image_advtab: true,
			});
	</script>
</main>
</body>
</html>