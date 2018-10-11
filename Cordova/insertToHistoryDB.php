<?php
	require_once 'Config.php'; 
	echo $has_power." || ".$UID." || ".$watthr."\r\n";
	if($has_power == "1" && $watthr > "0" )
	{
		$insert_query = "INSERT INTO t_history VALUES ('$UID', '$watthr',NOW(),NOW())";
		if($con->query($insert_query)){
			echo mysqli_error($con);
		}
	}
	
?>
