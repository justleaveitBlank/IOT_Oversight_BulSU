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
				// do something here if appliance have a limit value with update value to has_power to database if limit value/time reached 
			}
			else{
				include_once 'generate_json_has_power.php';
				echo "\r\n";
			}
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