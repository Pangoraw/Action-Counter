<!DOCTYPE html>
<html>
<head>
	<title>Remove a user</title>
</head>
<body>
<?php 
	$options = getOptions();
	$arg = 'mysql:host='.$options['hostname'].';dbname='.$options['databasename'];

	// Connect to the database 
	try
	{
		$dataBase = new PDO($arg, $options['username'] , $options['dbpassword']); 
	}
	catch (Exception $e)
	{
		die('Error : ' . $e->getMessage());
	}
	
	if (!empty($_POST['check_list'])) 
	{
		foreach ($_POST['check_list'] as $idChecked) 
		{
			$dataBase->exec(getCommandUser($idChecked));
			$dataBase->exec(getCommandAction($idChecked));
			print '<p>deleted id n°' .$idChecked.'</p>';
		}
	}
	header("location: ../../setup.php");

	function getCommandUser ( $id )
	{
		return 'DELETE FROM `user` WHERE id="'.$id.'"';
	}

	function getCommandAction ( $id )
	{
		return 'DELETE FROM `set` WHERE idUser="'.$id.'"';
	}

	function getOptions ()
	{
		$ini_array = parse_ini_file('../../data/options.ini');
		$ini_array['databasename'] = 'ActionCounter';
		return $ini_array ;
	}


 ?>
</body>
</html>