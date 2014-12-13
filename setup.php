	<?php 
		$options = getOptions();
		$arg = 'mysql:host='.$options['hostname'].';dbname='.$options['databasename'];

		// Connect to the database 
		$canConnect = true;
		try
		{
			$canConnect = true;
			$dataBase = new PDO($arg, $options['username'] , $options['dbpassword']	); 
		}
		catch (Exception $e)
		{
			$canConnect = false;
			/*die('Error : ' . $e->getMessage() );*/
		}
		if ($canConnect) 
		{
			$checkForSet   = $dataBase->prepare('SHOW TABLES LIKE "set"');
			$checkForUser  = $dataBase->prepare('SHOW TABLES LIKE "user"');
			$reqGetAll 	   = $dataBase->prepare('SELECT * FROM `user`');
			$reqTotalActer = $dataBase->prepare('SELECT COUNT(*) as total FROM `user`');


			$createTableUserReq = $dataBase->prepare('CREATE TABLE IF NOT EXISTS user
				(
					id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
					name VARCHAR(100) NOT NULL,
					num INT
				)');
			$createTableSetReq = $dataBase->prepare('CREATE TABLE IF NOT EXISTS `set` 
				(
					id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
					name VARCHAR(100),
					idUser INT,
					date DATE,
					action VARCHAR(100)
				)');			
		}

		function getTotalActer ( $req )
		{
			$totalActer = 0;
			$req->execute();
			while ($data = $req->fetch()) 
			{
				$totalActer = $data['total'];
			}
			return $totalActer;
		
		}

		function createSqlSetupForm ( $options )
		{	
			?>
			<h2 class="form-title" >Setup your MySQL Database</h2>
			<div class="form-div">
				<form action="script/php/edit_options.php" class="form" method="POST" >
					<ul>
						<li><label>Hostname</label><input type="text" name="hostname" value="<?php echo $options['hostname'] ?>" /></li>
						<li><label>Username</label><input type="text" name="username" value="<?php echo $options['username'] ?>" /></li>
						<li><label>Password</label><input type="password" name="dbpassword" value="<?php echo $options['dbpassword']  ?>" /></li>
						<li><label>Database Name</label><input type="text" name="databasename" value="<?php echo $options['databasename'] ?>"/></li>
			</div>
					<label><input type="submit" class="hidden-button" /><img alt="Submit" src="data/images/submit-button.svg"/></label>
				</ul>
			</form>
			<?php
		}

		function getOptions ()
		{
			$ini_array = parse_ini_file('data/options.ini');
			return $ini_array ;
		}

		function createFormToAdd ()
		{ ?>
			<h2 class="form-title" >Add acter</h2>
			<div class="form-div" >
				<form action="script/php/people_created.php" class="form" method="POST" >		
					<ul><li><label>Name</label><input type="text" name="name" required /></li></ul>
			</div>
					<label><input type="submit" class="hidden-button" /><img alt="Submit" src="data/images/submit-button.svg"/></label>
				</form>
		<?php 
		}

		function createFormToDelete ( $req )
		{
		?>
				
				<h2 class="form-title" >Delete acter</h2>
				<form action="script/php/delete_user.php" class="form" method="POST" > 
				<ul>

			<?php

			$req->execute();
			
			while ($data = $req->fetch()) 
			{
				echo '<li><input type="checkbox" name="check_list[]" id="acter" value="'. $data['id'] . '" /><label>'. $data['name'] .'</label></li>' ;
			}
			?>

				<label><input type="submit" class="hidden-button" /><img alt="Submit" src="data/images/submit-button.svg"/></label>
				</ul>
				</form>
				
		<?php
		}

		function createDefaultTable ( $reqSet, $reqUser )
		{

			$reqSet->execute();

			$reqUser->execute();

		}

		if ($canConnect) 
		{ 
			createDefaultTable( $createTableSetReq, $createTableUserReq ); 
			$totalActer 	 = getTotalActer( $reqTotalActer ); 
		}
	 ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Setup you Action Counter Server - DEV</title>
	<script type="text/javascript" src="script/js/jquery.min.js"></script>
	<link rel="stylesheet" href="stylesheet/setup.css" media="screen" />
	<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="data/fonts/stylesheet.css" media="screen" />
</head>
<body>
	<h1>Action Counter</h1>
	<div id="content" >
		<div class="form-wrapper" id="SQL_Setup">
			<?php 
				createSqlSetupForm( $options );	
			 ?>
		</div>

		<div class="form-wrapper" id="Delete_User" >
			<?php
				if ($totalActer != 0)
				{
					if ($canConnect) 
					{
						createFormToDelete( $reqGetAll ); 
					}
					else { echo '<h2 class="errorMessage" >You should setup your MY_SQL database first.</h2>'; }
				}
				else { echo '<h2 class="errorMessage" >You should create at least one user</h2>'; }
			?>			
		</div>

		<div class="form-wrapper" id="Add_User">
			<?php 
				if ($canConnect) { createFormToAdd(); }
				else { echo '<h2 class="errorMessage" >You should setup your MY_SQL database first.</h2>'; }
			 ?>
		</div>
	</div>
</body>
</html>