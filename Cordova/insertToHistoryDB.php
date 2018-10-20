<?php
	require_once 'Config.php';
	echo $has_power." || ".$UID." || ".$watthr." || ".$dateTime."\r\n";
	
	$insert_query = "INSERT INTO t_history VALUES ('$UID', '$watthr','$dateTime','$dateTime')";
	if($con->query($insert_query)){
		echo mysqli_error($con);
	}
	
?>
