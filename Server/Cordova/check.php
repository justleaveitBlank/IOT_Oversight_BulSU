<?php  
	require_once 'Config.php';

	$query = "SELECT * FROM t_appliance";
	$result = $con->query($query);

	$rows = array();
	if(mysqli_num_rows($result)>0){
		while($r = mysqli_fetch_assoc($result)) {
		    $rows[]=$r;
		}
		echo json_encode($rows);
	}
?>
	