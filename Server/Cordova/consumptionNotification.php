<?php 
	require_once 'Config.php';
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

		$query = 'INSERT INTO t_notification values ("","consumption","unresolved","'.$app_id.'")';
		$result = $con->query($query);
		if($result){
			$query = "SELECT * from t_appliance where uid = '$app_id'";
		
			$result = $con->query($query);
			if(mysqli_num_rows($result)==1)
			{
				while($row=mysqli_fetch_assoc($result)) {
					$id = $row['uid'];
					$name = $row['appl_name'];
					$consumption = $row['current_power_usage'];
					$limit = $row['power_limit_value'];
					?>
					<div class="col s12 m6">
						<div class="card">
							<div class="card-content white-text">
								<span class="card-title black-text">Consumption on Limit</span>
								<p class="black-text">ID: <?php echo $id?></p>
								<p class="black-text">Name: <?php echo $name?></p>
								<p class="black-text">Consumption: <?php echo $consumption?> kwh</p>
								<p class="black-text">Limit: <?php echo $limit?> kwh</p>
							</div>
							<div class="card-action right-align">
								<a class="btn-small waves-effect waves-light orange white-text modal-trigger sTitle" >Update</a>
								<a class="ignore btn-small waves-effect waves-light red white-text sTitle" >Ignore</a>
							</div>
						</div>
							
					</div>
					<?php
				}
			}else{
				echo "Failed" .  mysqli_error($con);
			}
		}
		else{
			echo "Failed" .  mysqli_error($con);
		}
	}
	?>
</body>
</html>