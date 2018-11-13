<?php 
	require 'Config.php';
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<?php 
	$db_host = "localhost";
	$db_username = "root";
	$db_password = "";
	$db_name = "oversight_prd";

	$con = mysqli_connect($db_host, $db_username, $db_password);
	if (!$con) {
	    die('Could not connect: ' . mysqli_connect_error());
	}
	mysqli_select_db($con,$db_name) or die("Connection failed");


	if(isset($_POST['app_id'])){
		$app_id = $_POST['app_id'];
		$_SESSION['newapp'] = $app_id; // inserts the uid of appliance to a session variable for registration later
		$query = 'INSERT INTO t_notification values ("","newapp","unresolved","'.$app_id.'")';
		$result = $con->query($query);
		if($result){
			?>
			<div class="col s12 m6">
				<div class="card">
					<div class="card-content white-text">
						<span class="card-title black-text">Unregistered Device</span>
						<p class="black-text">ID: <?php echo $app_id;?></p>
					</div>
					<div class="card-action right-align">
						<a class="btn-small waves-effect waves-light orange white-text sTitle" href="registerAppliance.html">Register</a>
						<a class="btn-small waves-effect waves-light green white-text modal-trigger sTitle" >Allow</a>
						<a class="ignore btn-small waves-effect waves-light red white-text sTitle" >Ignore</a>
					</div>
				</div>	
			</div>
			<?php
		}
		else{
			echo "Failed" .  mysqli_error($con);
		}
	}
	?>
</body>
</html>