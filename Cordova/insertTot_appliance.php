<?php
	require_once 'Config.php';
	$divisor = 0;
	
	$query = 'SELECT * FROM t_history';
	$result = $con->query($query);

	$query = 'SELECT * FROM t_appliance where uid="'.$UID.'"';
	$result = $con->query($query);
	
	if(mysqli_num_rows($result)==1){
		$upd_query = 'UPDATE t_appliance SET current_power_usage = ' . $power . ', avg_watthr = ' . $Kwatthr . '  WHERE uid = "' . $UID . '"';
		if($con->query($upd_query)){
			echo mysqli_error($con);
		}
	}
?>