<?php
	$bdd = new PDO('mysql:host=localhost;dbname=projet', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

	$name =$_POST['setter'];
	$actionType = 'basic';

	$req = $bdd->prepare('INSERT INTO `set`(`name`) VALUES(?)');
	$req->execute(array($name));

	$req2 = $bdd->prepare('UPDATE `set` SET `date` = CURDATE(), `action` = \'basic\' WHERE `name` = :nameSetter');
	$req2->execute(array(':nameSetter'=> $name));

?>	
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>New Action</title>
</head>
<body>
<?php 
	
	echo '<p>Registered Action for' . $name ;

	header("location: ../../index.php");

  ?>
</body>
</html>