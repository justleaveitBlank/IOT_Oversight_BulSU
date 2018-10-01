<?php  
	require_once 'Config.php';
	$query = 'SELECT * 
			  FROM t_notification 
			  WHERE type="newapp" and (status = "ignored" or status = "allowed" or status="unresolved") and appliance_id = "' . $appl_uid.'"';

	$result = $con->query($query);

	//--------------------------------------- CHECK IF EXISTING  ----------------------------
	if(mysqli_num_rows($result) > 0){
		$whilePluggedIn = true;
		$statusQuery = 'SELECT status 
						FROM t_notification 
						WHERE type="newapp" (status = "ignored" or status = "allowed") and appliance_id = "' . $appl_uid.'"';
		$statusResult = $con->query($query);
		if(mysqli_num_rows($statusResult) > 0 ){
			$updateQuery = 'UPDATE t_notification 
							SET status = "unresolved" 
							WHERE type="newapp" and (status = "ignored" or status = "allowed") and appliance_id = "' . $appl_uid.'"
							LIMIT 1,1';
			if($con->query($updateQuery)){
				echo mysqli_error($con);
			}
		}
	} 
	else{
		$query = 'INSERT INTO t_notification values ("","newapp","unresolved","'.$appl_uid.'")';
		if($con->query($query)){
			echo mysqli_error($con);
		}
	}
		
	
	$appliance_arr=array(
		"uid" => $appl_uid,
		"has_power" => "0",//value change depeding on the value of the user select 0/1
		"status" => "unregistered"
	);
	$json_has_power_data = json_encode($appliance_arr, JSON_PRETTY_PRINT);
	// get from signedPowerData.php since this is impoted
	//echo $UID."||". $voltage."||".$ampere."||". $power."||".$watthr."||".$date."||".$time."||".$timezone."\n\r";
	echo  $json_has_power_data ;
	
	//create json file
	file_put_contents('json_has_power_data.json',  $json_has_power_data);
	
?>