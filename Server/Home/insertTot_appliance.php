<?php
	require_once 'Config.php';
	$current_year=date('Y');
	$current_month=date('m');

	$query = "SELECT * FROM t_history 
			  WHERE uid='".$UID."' and  DATE(effective_date) BETWEEN '$current_year-$current_month-01' AND '$current_year-$current_month-31'";
	$result = $con->query($query);
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()) {
			// extract row
			// this will make $row['name'] to
			// just $name only
			extract($row);
			$m_consumed = $m_consumed + $consumed; // watthr
			$m_avg_watthr = $m_consumed/30;
			$m_Kwatthr = $m_consumed/1000;
			$e_price = $m_Kwatthr * $s_price_rate;
			$e_price = round($e_price,2);
			//echo $m_Kwatthr."\r\n";
			//need to update
			
			if($has_power =="1"){
				if($lstMoUpdate != $current_month){
					$upd_query = 'UPDATE t_appliance SET current_power_usage = 0 ,estimated_cost= 0  WHERE uid = "' . $UID . '"';
					if($con->query($upd_query)){
						echo mysqli_error($con);
					}
					$lstMoUpdate = $current_month;
					file_put_contents("textVariables.txt","lastUID=".$UID ."||currentConsumedVal=0||lastMoUpdate=".$lstMoUpdate);
				}
				else{
					$upd_query = 'UPDATE t_appliance SET current_power_usage = ' . $m_consumed . ', avg_watthr = ' . round($m_avg_watthr,2) . ',estimated_cost= '.$e_price.'  WHERE uid = "' . $UID . '"';
					if($con->query($upd_query)){
						echo mysqli_error($con);
					}
				}
				
			}
			
		}
	}
	//echo $e_price."\r\n";
	//$query = "SELECT * FROM t_appliance WHERE uid='".$UID."'";
	//$result = $con->query($query);
	
	//$row = $result->fetch_object();
	//$s_socket = $row->avg_watthr;
?>