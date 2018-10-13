<?php
	require_once 'Config.php';
	$query = "SELECT * from t_settings";
	$result = $con->query($query);
	
	$row = $result->fetch_object();
	$s_socket = $row->socket;
	$s_limitation = $row->limitation;
	$s_authentication = $row->authentication;
	
	//echo $UID."||". $voltage."||".$ampere."||". $power."||".$watthr."||".$date."||".$time."||".$timezone."</br>";
	//echo $s_socket." || ".$s_limitation." || ".$s_authentication."\n\r";
	//------------------------------------- CHECK SOCKET -------------------------------------//
	if($s_socket == "true"){
		//------------------------------------- AUTHENTICATION -------------------------------------//
		if($s_authentication == "true"){
			//------------------------------------- LIMITATION HERE -------------------------------------//
			if($s_limitation == "true"){
				$query ='SELECT * 
						FROM t_appliance
						WHERE appl_name="Anonymous_Appliance"';
				$has_power="0";
				$result = $con->query($query);
				$appl_rows = $result->fetch_object();
				$appl_Auid = $appl_rows->uid;
				$appl_has_time_limit = $appl_rows->has_time_limit;
				$appl_time_limit_value = $appl_rows->time_limit_value;
				
				if($UID==$appl_Auid){
					if($appl_has_time_limit == "1"){
						if(date("Y-m-d h:i:s")<$appl_time_limit_value){
							$has_power="1";
						}
						else{
							$query ='UPDATE t_appliance set uid="NO_UID"
									WHERE appl_name="Anonymous_Appliance"';
							$result = $con->query($query);
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