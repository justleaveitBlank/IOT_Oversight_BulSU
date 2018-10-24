<?php
	require_once 'Config.php';
	$query = "SELECT * from t_settings";
	$result = $con->query($query);

	$row = $result->fetch_object();
	$s_socket = $row->socket;
	$s_limitation = $row->limitation;
	$s_authentication = $row->authentication;
	$s_price_rate = $row->price;

	//echo $UID."||". $voltage."||".$ampere."||". $power."||".$watthr."||".$date."||".$time."||".$timezone."</br>";
	//echo $s_socket." || ".$s_limitation." || ".$s_authentication."\n\r";
	//------------------------------------- CHECK SOCKET -------------------------------------//
	if($s_socket == "true"){
		//------------------------------------- AUTHENTICATION -------------------------------------//
		if($s_authentication == "true"){
			//------------------------------------- LIMITATION HERE -------------------------------------//
			if($s_limitation == "true"){
				//Check whether temporary appliance have an id or not
				$nameToCompare = ($UID == "NO_UID")? "Anonymous_Appliance" : "Unregistered_Appliance";
				//check if device already allowed or in other words already registered
				$query ='SELECT *
						FROM t_appliance
						WHERE appl_name="' .$nameToCompare. '"';
				$has_power="0";
				$result = $con->query($query);
				if(mysqli_num_rows($result)>0){
					$appl_rows = $result->fetch_object();
					$appl_Auid = $appl_rows->uid;
					$appl_has_time_limit = $appl_rows->has_time_limit;
					$appl_time_limit_value = $appl_rows->time_limit_value;
					if($UID==$appl_Auid){
						$flag = 0;
						if($appl_has_time_limit == "1"){
							$date = strtotime($appl_time_limit_value);
							if(time()<$date || $date==strtotime("0000-00-00 00:00:00")){
								$has_power="1";
								$query = "UPDATE t_appliance SET has_time_limit = 1 WHERE appl_name = '".$nameToCompare."'";
								$result = $con->query($query);
								$notifStat = "false";
								$aDevice = "3";
								$flag = 1;
							}
						}
						if($flag == 0){
							if($UID != "NO_UID"){
								$notifStat = "true";
								$aDevice = "1";
								if($UID = "UNPLUGGED"){
									$notifStat = "false";
									$aDevice = "0";
								}
							} else {
								$aDevice = "1";
								$notifStat = "true";
							}

							$query = "DELETE FROM t_appliance WHERE appl_name = '".$nameToCompare."'";
							$result = $con->query($query);
						}
					}
				}
			}

			//Check consumption vs limit take id if appliance exceed warning level
			$query = "SELECT IF(current_power_usage >= (power_limit_value*0.85), 'OVERCONSUMING', 'NORMAL') ConsumptionStatus
								FROM t_appliance
								WHERE power_limit_value>0 and uid = '".$UID."'";
			$result = $con->query($query);
			if(mysqli_num_rows($result)==1){
				while ($row = mysqli_fetch_assoc($result)) {
					$consumer_status = $row['ConsumptionStatus'];
					if($consumer_status != "NORMAL"){
						$query = "SELECT IFNULL(MAX(notif_id),'NULL') as notif_id FROM t_notification WHERE type = 'consumption' and appliance_id = '".$UID."'";
						$notif_results = $con->query($query);
						if(mysqli_num_rows($notif_results)>0){
							while ($notif_row = mysqli_fetch_assoc($notif_results)) {
								$target_notif = $notif_row['notif_id'];
								$query = "";
								if($target_notif != "NULL"){
									$query = "UPDATE t_notification SET status = 'unresolved' WHERE notif_id = ".$target_notif;
								} else {
									$query = "INSERT INTO t_notification (`type`, `status`, `appliance_id`) VALUES ('consumption','unresolved','".$UID."')";
								}
								$con->query($query);
							}
						}
					}
				}
			}

			include_once 'generate_json_has_power.php';
			echo "\r\n";
		}
		else{
			$has_power="1";
			$appliance_arr=array(
				"has_power" => $has_power,//value change depeding on the value of the user select 0/1
				"socket_status" => "Socket On",
				"authentication" => $s_authentication
			);
			include_once 'insertToHistoryDB.php';
			$json_has_power_data = json_encode($appliance_arr, JSON_PRETTY_PRINT);
			echo  $json_has_power_data ;

			//create json file
			file_put_contents('json_has_power_data.json',  $json_has_power_data);
		}
	}
	else{
		$has_power="0";
		$appliance_arr=array(
			"has_power" => $has_power,//value change depeding on the value of the user select 0/1
			"socket_status" => "Socket Off",
			"authentication" => $s_authentication
		);
		$json_has_power_data = json_encode($appliance_arr, JSON_PRETTY_PRINT);
		echo  $json_has_power_data ;

		//create json file
		file_put_contents('json_has_power_data.json',  $json_has_power_data);
	}
?>
