<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF_8" />
	<title>Creation de personnes</title>
</head>
<body>
	<?php 

		$options = getOptions();
		$arg = 'mysql:host='.$options['hostname'].';dbname='.$options['databasename'];

		// Connect to the database 
		$dataBase = new PDO($arg, $options['username'] , $options['dbpassword'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)); 
	

		$req = $dataBase->prepare('INSERT INTO user(name) VALUES(?)');
		$req->execute(array($_POST['name']));
		echo '<p>' . $_POST['name'] . ' has been added to the database.</p>';
		header("location: ../../setup.php");

		function getOptions ()
		{
			$ini_array = parse_ini_file('../../data/options.ini');
			return $ini_array ;
		}

	 ?>
</body>
</html>