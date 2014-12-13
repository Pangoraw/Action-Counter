<?php
	$options = getOptions();
	print_r($options);
	$arg = 'mysql:host='.$options['hostname'].';dbname='.$options['databasename'];

	// Connect to the database
	try
	{
		$dataBase = new PDO($arg, $options['username'] , $options['dbpassword']); 
	}
	catch (Exception $e)
	{
		die ('Error : ' . $e->getMessage());
	}

	$id = $_POST['idUser'];
	echo '<p>Registered Action for' . $id ;

	$reqSet = $dataBase->prepare('INSERT INTO `set`(`idUser`, `name`, `date`, `action`) VALUES(:idUser, :name, CURDATE(), "basic")');

	$reqName = $dataBase->prepare('SELECT name FROM `user` WHERE id=?');
	$reqName->execute(array($id));
	

	while ($data = $reqName->fetch()) 
	{		
		echo $data['name'];
		$reqSet->execute(array(
			'idUser' => $id,
			'name'   => $data['name']
			));
	}

	function getOptions ()
	{
		$ini_array = parse_ini_file('../../data/options.ini');
		return $ini_array ;
	}


?>	
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>New Action</title>
</head>
<body>
<?php 
	header('Location: ../../index.php');

  ?>
</body>
</html>