<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Projet - Home - DEV</title>
	<link href="stylesheet/index.css" rel="stylesheet" media="all" type="text/css" ></link>
	<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="data/fonts/stylesheet.css" media="screen" />
	<script type="text/javascript" src="script/js/googlejsapi.js"></script>
	<script type="text/javascript" src="script/js/jquery.min.js"></script>
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
		header('Location: setup.php');
	}
	
	// Prepare the MySql request's code
	$reqName = $dataBase->prepare('SELECT * FROM `user`');
	$reqActerName = $dataBase->prepare('SELECT COUNT(*) as countByName, name FROM `set` WHERE name = :name ');
	$reqTotalActer = $dataBase->prepare('SELECT COUNT(*) as total FROM `user`');
	$reqTotalAction = $dataBase->prepare('SELECT COUNT(*) as total FROM `set`');

	?>

	<h1>Action Counter</h1>
	
	<?php 

	createRadioForm($reqName);

	 ?>	
	<div id="chart-div" ></div>
	<?php 

	$acterStat = countByName($reqName, $reqActerName);

	$totalActer = getTotalActer($reqTotalActer);
	$totalAction = getTotalAction($reqTotalAction);

	?>
	<?php 

	function getOptions ()
	{
		$ini_array = parse_ini_file('data/options.ini');
		$ini_array['databasename'] = 'ActionCounter';
		return $ini_array ;
	}


	function createRadioForm ( $req ) // Function which is in charge to create the radio formular 
	{	?>
		<div class="form-wrapper" >
		<h2 class="form-title" >Select a new action maker.</h2>
		<form action="script/php/set.php" class="form" method="POST">
		<div class="form-div" >
		<ul>
		<?php
		$i = 0;
		$req->execute();
		while ($data = $req->fetch()) {
			$userId   = $data['id'];
			$userName = $data['name'];
			$i = $i + 1;

			if ($i > 1) {

				echo '<li><input type="radio" name="idUser" value="' . $userId . '"  /><label>'. $userName . '</label></li>' ;

			}
			else {

				echo '<li><input type="radio" name="idUser" value="' . $userId . '" checked/><label>'. $userName . '</label></li>' ;

			}
		}?>
			</ul>
			</div>
			<label><input type="submit" id="button" class="hidden-button"/><img alt="Submit" src="data/images/submit-button.svg"></label>
			</form>
		</div>
	<?php
	}

	function countByName($reqName, $reqCountByName) // Return a string of the form '#UserName#UserActionTimeUser2Name#User2ActionTime etc ...'
	{
		$acterStat = '';
		$reqName->execute();
		while ($data = $reqName->fetch()) {
			$reqCountByName->execute(array(
				'name' => $data['name']
				));
			$currentUserName = $data['name'];

			while ($data = $reqCountByName->fetch()) {
				$acterStat = $acterStat . '#' . $currentUserName . '#' . $data['countByName'] ;
			}
		}
		return $acterStat ;
	}

	function getTotalActer($reqTotalActer) // Return the number of total acter
	{
		$totalActer = 0;
		$reqTotalActer->execute();
		while ($data = $reqTotalActer->fetch()) 
		{
			$totalActer = $data['total'];
		}
		return $totalActer;
	}

	function getTotalAction($reqTotalAction) // Return the number of total action
	{
		$totalAction = 0;
		$reqTotalAction->execute();
		while ($data = $reqTotalAction->fetch()) {
			$totalAction = $data['total'];
		}
		return $totalAction;
	}

	 ?>
	<script type="text/javascript">
		var acterStr = "<?php echo $acterStat ; ?>";

		var acterGlobal = treatString(acterStr);

		google.load('visualization', '1.0', {'packages': ['corechart']});

		google.setOnLoadCallback(drawchart);

		function drawchart () // In charge of drawing the Pie chart
		{
			var data = new google.visualization.DataTable();

			data.addColumn('string', 'Acter Name');
			data.addColumn('number', 'Number of action done');

			for (i = 0 ; i < acterGlobal.length ; i++ ) {
				if (typeof acterGlobal[i] != undefined || typeof acterGlobal[i+1] != undefined) {
					data.addRows([
						[acterGlobal[i], parseFloat(acterGlobal[i+1])]
						]);					
				}
			}

			var options = { title: 'Action Repartition',
							width: '400',
							height: '300',
							legend: 'none',
							backgroundColor: '#2b1100',
							pieSliceTextStyle: {'color': 'white'},
							titleTextStyle: {'color': 'white', bold: true},
							tooltip: {textStyle: { color:'black' } }
						};

			var chart = new google.visualization.PieChart(document.getElementById('chart-div'));
			chart.draw(data, options);
		}

		function treatString (acterStat) // Cut the string and return stats and names in one global array
		{
			var acterGlobal = [];
			var strCut = acterStat.split('#');
			for(i = 0; i < strCut.length; i = i + 2) {
				acterGlobal[i] = strCut[i] ;
				acterGlobal[i + 1] = strCut[i + 1];
			}
			for (i = 0; i < acterGlobal.length; i++) {
				acterGlobal[i-1] = acterGlobal[i];
			}
			return acterGlobal;
		}
	</script>
	<a href="setup.php" >Setup you Action's Server</a>
</body>
</html>