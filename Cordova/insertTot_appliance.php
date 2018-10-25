<?php
	require_once 'Config.php';
	$current_date=date('Y-m-d');
	$current_year=date('Y');
	$current_month=date('m');
	
	$query = "SELECT * FROM t_history 
			  WHERE uid='".$UID."' and  DATE(effective_date) BETWEEN '$current_year-$current_month-01' AND '$current_year-$current_month-30'";
	$result = $con->query($query);
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()) {
			// extract row
			// this will make $row['name'] to
			// just $name only
			extract($row);
			$ctr++;
			$m_consumed = $m_consumed+$consumed;
			
			$m_avg_watthr = $m_consumed/$ctr;
			$Kwatthr=$m_avg_watthr/1000;
			//echo $ctr." || ".$consumed." || ".$effective_date." || ".$m_consumed." || ".$m_avg_watthr." || ".$Kwatthr."\r\n";
			$e_price =$m_consumed*$s_price_rate;
			
			$s_query = 'SELECT * FROM t_appliance where uid="'.$UID.'"';
			$result = $con->query($s_query);
			if(mysqli_num_rows($result)==1){
				$row = $result->fetch_object();
				$c_avg_watthr = $row->avg_watthr;
				if($Kwatthr > $c_avg_watthr){
					$upd_query = 'UPDATE t_appliance SET current_power_usage = ' . $power . ', avg_watthr = ' . $Kwatthr . ',estimated_cost= '.$e_price.'  WHERE uid = "' . $UID . '"';
					if($con->query($upd_query)){
						echo mysqli_error($con);
					}
				}
			}
			
			
		}
	}
	echo $e_price."\r\n";
	//$query = "SELECT * FROM t_appliance WHERE uid='".$UID."'";
	//$result = $con->query($query);
	
	//$row = $result->fetch_object();
	//$s_socket = $row->avg_watthr;
?>