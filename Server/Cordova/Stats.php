<?php
	require_once 'Config.php';
	
// ===================================== STATS COMPUTATION SECTION =============================================
	
	// ======================================= WEEKLY GRAPH =============================================
	if(isset($_POST['getComputeWeekly'])){
		$valid = checkHistoryData();
		if($valid){
			$givenDate = ($_POST['getComputeWeekly']=="now")? date("Y-m-d") : $_POST['getComputeWeekly'];
			$sort= $_POST['sort'];
			$searched=  "%". $_POST['searched'] ."%";
			$appCon = array();
			$weekArray = array();
			$dayOfGiven = date('N', strtotime($givenDate));
			$dayToSubtract = (($dayOfGiven) == 7)? 0 : $dayOfGiven;
			$dateStart = date('Y-m-d', strtotime($givenDate. ' - '.$dayToSubtract.' days'));
			$dateEnd = date('Y-m-d', strtotime($dateStart. ' + 6 days'));
			$query = "SELECT DC.uid , DC.appl_name, DC.appl_type ,DC.consumed FROM (SELECT H.uid as uid , IFNULL(A.appl_name,IF(H.uid = 'NO_UID','Anonymous',CONCAT('Unregistered - ',H.uid))) as appl_name, IFNULL(A.appl_type,'GENERAL APPLIANCE') as appl_type, SUM(H.consumed) as consumed FROM t_history as H LEFT JOIN t_appliance as A ON H.uid = A.uid WHERE DATE(H.effective_date) BETWEEN ? AND ? GROUP BY H.uid, A.appl_name, A.appl_type) as DC WHERE DC.appl_name LIKE ? OR DC.appl_type LIKE ? ORDER BY DC.".$sort;
			$stmt = $con->prepare($query);
			$stmt->bind_param("ssss", $dateStart, $dateEnd , $searched , $searched);
			$stmt->execute();
			$stmt->bind_result($uid, $name, $type, $consumption);
			while ($stmt->fetch()) {
				$appArray = array();
				array_push($appArray, array("consumption" => $consumption,"name"=>$name,"type"=>$type));
				array_push($weekArray, array($uid => $appArray));
			}
			echo json_encode($weekArray);
			$stmt->close();
		} else {
			echo "No Current Data";
		}
	}
	
	// ======================================= MONTHLY STATS =============================================
	if(isset($_POST['getComputeMonthly'])){
		$valid = checkHistoryData();
		if($valid){
			$givenDate = ($_POST['getComputeMonthly']=="now")? date("Y-m-d") : $_POST['getComputeMonthly'];
			$sort= $_POST['sort'];
			$searched=  "%". $_POST['searched'] ."%";
			$appCon = array();
			$monthArray = array();
			$query = "SELECT DC.uid , DC.appl_name, DC.appl_type,DC.consumed FROM (SELECT H.uid as uid , IFNULL(A.appl_name,IF(H.uid = 'NO_UID','Anonymous',CONCAT('Unregistered - ',H.uid))) as appl_name, IFNULL(A.appl_type,'GENERAL APPLIANCE') as appl_type, SUM(H.consumed) as consumed FROM t_history as H LEFT JOIN t_appliance as A ON H.uid = A.uid WHERE YEAR(H.effective_date) = YEAR(?) and MONTH(H.effective_date) = MONTH(?) GROUP BY H.uid, A.appl_name,A.appl_type) as DC WHERE DC.appl_name LIKE ? OR DC.appl_type LIKE ? ORDER BY DC.".$sort;
			$stmt = $con->prepare($query);
			$stmt->bind_param("ssss", $givenDate,$givenDate,$searched,$searched);
			$stmt->execute();
			$stmt->bind_result($uid, $name, $type, $consumption);
			while ($stmt->fetch()) {
				$appArray = array();
				array_push($appArray, array("consumption" => $consumption,"name"=>$name,"type"=>$type));
				array_push($monthArray, array($uid => $appArray));
			}
			echo json_encode($monthArray);
			$stmt->close();
		} else {
			echo "No Current Data";
		}
	}
	
	// ======================================= YEARLY STATS =============================================
	if(isset($_POST['getComputeYearly'])){
		$valid = checkHistoryData();
		if($valid){
			$givenDate = ($_POST['getComputeYearly']=="now")? date("Y-m-d") : $_POST['getComputeYearly'];
			$sort= $_POST['sort'];
			$searched=  "%". $_POST['searched'] ."%";
			$appCon = array();
			$yearArray = array();
			$query = "SELECT DC.uid , DC.appl_name , DC.appl_type, DC.consumed FROM (SELECT H.uid as uid , IFNULL(A.appl_name,IF(H.uid = 'NO_UID','Anonymous',CONCAT('Unregistered - ',H.uid))) as appl_name, IFNULL(A.appl_type,'GENERAL APPLIANCE') as appl_type, SUM(H.consumed) as consumed FROM t_history as H LEFT JOIN t_appliance as A ON H.uid = A.uid WHERE YEAR(H.effective_date) = YEAR(?) GROUP BY H.uid, A.appl_name, A.appl_type) as DC WHERE DC.appl_name LIKE ? OR DC.appl_type LIKE ? ORDER BY DC.".$sort;
			$stmt = $con->prepare($query);
			$stmt->bind_param("sss", $givenDate,$searched , $searched);
			$stmt->execute();
			$stmt->bind_result($uid, $name, $type, $consumption);
			while ($stmt->fetch()) {
				$appArray = array();
				array_push($appArray, array("consumption" => $consumption,"name"=>$name,"type"=>$type));
				array_push($yearArray, array($uid => $appArray));
			}
			echo json_encode($yearArray);
			$stmt->close();
		} else {
			echo "No Current Data";
		}
	}
	
	// ===================================== PRODUCE APPLIANCE INFORMATION =============================================
	
	if(isset($_POST['applianceSummary'])){
		$uid = $_POST['applianceSummary'];
		$sum = $_POST['total'];
		$price = $_POST['price'];
		$color = $_POST['color'];
		$divisor = intval($_POST['divisor']);

		$avg = $sum/$divisor;
		$ep = $sum/1000*$price;
		//echo number_format($sum,2)." = ".number_format($sum/1000,2);
		$query = "SELECT IFNULL((SELECT appl_name FROM t_appliance WHERE uid = '".$uid."'),'Unregistered - ".$uid."') as appl_name , IFNULL((SELECT appl_type FROM t_appliance WHERE uid = '".$uid."'),'GENERAL APPLIANCE') as appl_type";
		$result = $con->query($query);
		if(mysqli_num_rows($result)>0){
			while($row = mysqli_fetch_assoc($result)){
				$AppName = ($uid == "NO_UID")? 'Anonymous Appliance' : $row['appl_name'];
				$type = $row['appl_type'];
			if ((strpos($AppName, 'Unregistered') === false) && (strpos($AppName, 'Anonymous') === false)) {
				$AppName = $AppName . " (" .$type. ") ";
			}
			  
			  
			if(number_format($sum/1000,2) == 0.00){
				$sum_out = number_format($sum,2)." Wh";
			}
			else{
				$sum_out = number_format($sum/1000,2)." kWh";
			}
			  
			if(number_format($avg/1000,2) == 0.00){
				$avg_out = (number_format($avg,2))." Wh";
			}
			else{
				$avg_out = (number_format($avg/1000,2))." kWh";
			}
			  
			?>

			<div class="row" name="<?php echo $uid;?>" style="margin-bottom:0">
				<div class="divider topNbotMarginer"></div>
				<div class="col s12">
					<div class="applName" style="background-color:<?php echo $color?>;"><?php echo $AppName;?></div>
					
					<div class="applDetails" style="display: inline-block; border:solid <?php echo $color?> 1px ; border-radius: 0 0 3px 3px; padding: .2rem .5rem;">
						<div class="col s12"><b>Appliance ID: </b> <span><?php echo $uid;?></span></div>
						<div class="col s12"><b>Appliance Type: </b> <span><?php echo $type;?></span></div>
						<div class="col s12"><b>Average Kwatthr :</b> <span><?php echo  $avg_out;?></span></div>
						<div class="col s12"><b>Total Consumption :</b> <span><?php echo $sum_out;?></span></div>
						<div class="col s12"><b>Estimated Price :</b> <span><?php echo "₱ " .sprintf('%0.2f',$ep);?></span></div>
					</div>
				</div>
			</div>
			<?php
			}
		}
	}
	
// ================================ GRAPHICAL REPRESSENTATION SECTION =============================================
	
	// ======================================= WEEKLY GRAPH =============================================
	if(isset($_POST['getweekly'])){
		$consumptionarray = array();
		$singleAppArray = array();
		$singleAppName = array();
		$daysarray = array("SUN","MON","TUE","WED","THU","FRI","SAT");
		$day = intval($_POST['Day']);
		$getdate = ($_POST['getweekly']=="now")? date("Y-m-d") : $_POST['getweekly'];
		//get how many days from sunday and then subtract to getdate to get sunday's date
		$newday = $day - 1;
		$date = date('Y-m-d', strtotime($getdate. ' - ' . $newday . ' days'));
		$finalarray = array(0,0,0,0,0,0,0);
		$valid = checkHistoryData();
		if($valid){
			//------------------------------OVERALL WEEKLY--------------------------
			for ($i=0; $i < 7; $i++) {
				$curdate = date('Y-m-d', strtotime($date. ' + ' . $i . ' days'));
				$select = "IFNULL(SUM(consumed),0)  as  consumed";
				$table = "t_history";
				$where = "DATE(effective_date) = DATE('".$curdate."')";
				$result = processQuery($select,$table,$where);
				if(mysqli_num_rows($result)>0){
					while($row = mysqli_fetch_assoc($result)){
						$finalarray[$i] = sprintf('%0.2f',$row["consumed"]);
					}
				} else {
					print mysqli_error($con);
				}
			}

			//------------------------------SINGLE APP WEEKLY-----------------------
			$startDate = $date;
			$endDate = date('Y-m-d', strtotime($date. ' + 6 days'));
			$applianceQuery = "SELECT DISTINCT(uid) as uid FROM t_history WHERE DATE(effective_date) between DATE('".$startDate."') and DATE('".$endDate."')";
			$applianceResults = $con->query($applianceQuery);
			if(mysqli_num_rows($applianceResults)>0){
				while($appRow = mysqli_fetch_assoc($applianceResults)){
				$currentUID = $appRow['uid'];
					if($currentUID == 'NO_UID'){
						array_push($singleAppName,'Anonymous');
					} else {
						$applianceInfo = "SELECT IFNULL((SELECT IFNULL(appl_name,'Unregistered') as appl_name FROM t_appliance WHERE uid='".$currentUID."'),'Unregistered') as appl_name";
						$applianceRes = $con->query($applianceInfo);
						if(mysqli_num_rows($applianceRes)>0){
							while($applset = mysqli_fetch_assoc($applianceRes)){
								array_push($singleAppName,$applset['appl_name']);
							}
						}
					}
				$singleAppConsumption = array();
				//$curdateArray = array();
					for ($i=0; $i < 7; $i++) {
						$curdate = date('Y-m-d', strtotime($date. ' + ' . $i . ' days'));
						$consumptionQuery = "SELECT IFNULL((SELECT IFNULL(SUM(consumed),0) FROM t_history WHERE uid='".$currentUID."' and DATE(effective_date) = DATE('".$curdate."')),0) as consumed";
						//array_push($curdateArray,$consumptionQuery);
						$consumptionResults = $con->query($consumptionQuery);
						if(mysqli_num_rows($consumptionResults)>0){
							while($conRow = mysqli_fetch_assoc($consumptionResults)){
								$currentConsumption = sprintf('%0.2f',$conRow['consumed']);
								array_push($singleAppConsumption, sprintf('%0.2f', $currentConsumption));
							}
						} else {
							print mysqli_error($con);
						}
					}
					array_push($singleAppArray, array($currentUID => $singleAppConsumption));
				}
			}
		}
		echo json_encode($daysarray)."|".json_encode($finalarray)."|".json_encode($singleAppArray)."|".json_encode($singleAppName);
	}

	// ======================================= MONTHLY GRAPH =============================================
	if(isset($_POST['getmonthly'])){
		$finalarray = array();
		$singleAppArray = array();
		$monthsarray = array();
		$singleAppName = array();
		$date = ($_POST['getmonthly']=="now")? date("Y-m-d") : $_POST['getmonthly'];
		$maxDay = intval(($_POST['getmonthly']=="now")? date("t") : date("t",strtotime($_POST['getmonthly'])));
		
		$MonthNow = ($_POST['getmonthly']=="now")? date("M") : date("M",strtotime($_POST['getmonthly']));
		$YearNow = ($_POST['getmonthly']=="now")? date("Y") : date("Y",strtotime($_POST['getmonthly']));
		
		$dtStart = new DateTime('first day of '.$MonthNow.' this year');
		$dtEnd = new DateTime('last day of '.$MonthNow.' this year');
		$WeekStart = intval($dtStart->format('W'))-1;
		$WeekEnd = intval($dtEnd->format('W'))-1;
		
		if($MonthNow == "Dec"){
			$WeekEnd = intval(getLastWeek($YearNow));
		}
		
		$valid = checkHistoryData();
		if($valid){
			//------------------------------OVERALL MONTHLY--------------------------
			$consumptionarray = array();
			$WeekCtr = 0; 
			for ($i=$WeekStart; $i <=$WeekEnd; $i++) {
				$WeekCtr++;
				array_push($monthsarray, ("Week ". $WeekCtr));
				array_push($finalarray,0);
				$select = "IFNULL(SUM(consumed), 0) as  consumed";
				$table = "t_history";
				$where = "YEAR(effective_date) = YEAR('".$date."') and MONTH(effective_date) = MONTH('".$date."') and WEEK(effective_date) = " . $i;
				$result = processQuery($select,$table,$where);

				if(mysqli_num_rows($result)>0){
					while($row = mysqli_fetch_assoc($result)){
						array_push($consumptionarray,number_format(sprintf('%0.2f', $row["consumed"]),2));
					}
				} else {
					print mysqli_error($con);
				}
			}
			
			for ($j=0; $j < sizeof($finalarray); $j++) {
				$finalarray[$j] = $consumptionarray[$j];
			}
		  
			//------------------------------SINGLE APP MONTHLY--------------------------
			$applianceQuery = "SELECT DISTINCT(uid) as uid FROM t_history WHERE YEAR(effective_date) = YEAR('".$date."') and MONTH(effective_date) = MONTH('".$date."')";
			$applianceResults = $con->query($applianceQuery);
			if(mysqli_num_rows($applianceResults)>0){
				while($appRow = mysqli_fetch_assoc($applianceResults)){
				$currentUID = $appRow['uid'];
					if($currentUID == 'NO_UID'){
						array_push($singleAppName,'Anonymous');
					} else {
						$applianceInfo = "SELECT IFNULL((SELECT IFNULL(appl_name,'Unregistered') as appl_name FROM t_appliance WHERE uid='".$currentUID."'),'Unregistered') as appl_name";
						$applianceRes = $con->query($applianceInfo);
						if(mysqli_num_rows($applianceRes)>0){
							while($applset = mysqli_fetch_assoc($applianceRes)){
								array_push($singleAppName,$applset['appl_name']);
							}
						}
					}
					
				$singleAppConsumption = array();
				for ($i=$WeekStart; $i <=$WeekEnd; $i++) {
					$consumptionQuery = "SELECT IFNULL((SELECT IFNULL(SUM(consumed),0) FROM t_history WHERE uid='".$currentUID."' and YEAR('".$date."') and MONTH(effective_date) = MONTH('".$date."') and WEEK(effective_date) = ".$i."),0) as consumed";
					$consumptionResults = $con->query($consumptionQuery);
					if(mysqli_num_rows($consumptionResults)>0){
						while($conRow = mysqli_fetch_assoc($consumptionResults)){
							$currentConsumption = $conRow['consumed'];
							array_push($singleAppConsumption,sprintf('%0.2f',$currentConsumption));
						}
					} else {
						echo mysqli_error($con);
					}
					
				}
				
				array_push($singleAppArray, array($currentUID => $singleAppConsumption));
				}
			}
		}
		echo json_encode($monthsarray)."|".json_encode($finalarray)."|".json_encode($singleAppArray)."|".json_encode($singleAppName);
	}
	
	// ======================================= YEARLY GRAPH =============================================
	if(isset($_POST['getyearly'])){
		$singleAppArray = array();
		$finalarray = array();
		$monthsarray = array();
		$months = array(
			'January',
			'February',
			'March',
			'April',
			'May',
			'June',
			'July ',
			'August',
			'September',
			'October',
			'November',
			'December',
		);
		$singleAppName = array();
		$date = ($_POST['getyearly']=="now")? date("Y-m-d") : $_POST['getyearly'];

		$valid = checkHistoryData();
		if($valid){
			//------------------------------OVERALL YEARLY--------------------------
			$consumptionarray = array();
			for ($i=1; $i <= 12; $i++) {
				array_push($monthsarray,$months[$i-1]);
				array_push($finalarray,0);
				$select = "IFNULL(SUM(consumed), 0) as  consumed";
				$table = "t_history";
				$where = "YEAR(effective_date) = YEAR('".$date."') and MONTH(effective_date) = " . $i;
				$result = processQuery($select,$table,$where);

				if(mysqli_num_rows($result)>0){
					while($row = mysqli_fetch_assoc($result)){
						array_push($consumptionarray,number_format(sprintf('%0.2f', $row["consumed"]),2));
					}
				} else {
					print mysqli_error($con);
				}
			}
			
			for ($j=0; $j < sizeof($finalarray); $j++) {
				$finalarray[$j] = $consumptionarray[$j];
			}
		  
			//------------------------------SINGLE APP YEARLY--------------------------
			$applianceQuery = "SELECT DISTINCT(uid) as uid FROM t_history WHERE YEAR(effective_date) = YEAR('".$date."') ";
			$applianceResults = $con->query($applianceQuery);
			if(mysqli_num_rows($applianceResults)>0){
				while($appRow = mysqli_fetch_assoc($applianceResults)){
					$currentUID = $appRow['uid'];
					if($currentUID == 'NO_UID'){
						array_push($singleAppName,'Anonymous');
					} else {
						$applianceInfo = "SELECT IFNULL((SELECT IFNULL(appl_name,'Unregistered') as appl_name FROM t_appliance WHERE uid='".$currentUID."'),'Unregistered') as appl_name";
						$applianceRes = $con->query($applianceInfo);
						if(mysqli_num_rows($applianceRes)>0){
						  while($applset = mysqli_fetch_assoc($applianceRes)){
							  array_push($singleAppName,$applset['appl_name']);
						  }
						}
					}
						
					$singleAppConsumption = array();
					for ($i=1; $i <=12; $i++) {
						$consumptionQuery = "SELECT IFNULL((SELECT IFNULL(SUM(consumed),0) FROM t_history WHERE uid='".$currentUID."' and YEAR('".$date."') and MONTH(effective_date) =  ".$i."),0) as consumed";
						$consumptionResults = $con->query($consumptionQuery);
						if(mysqli_num_rows($consumptionResults)>0){
						  while($conRow = mysqli_fetch_assoc($consumptionResults)){
							  $currentConsumption = $conRow['consumed'];
							  array_push($singleAppConsumption,sprintf('%0.2f',$currentConsumption));
						  }
						} else {
							print mysqli_error($con);
						}
						
					}
					
					array_push($singleAppArray, array($currentUID => $singleAppConsumption));
				}
			}
		}
		echo json_encode($monthsarray)."|".json_encode($finalarray)."|".json_encode($singleAppArray)."|".json_encode($singleAppName);
	}
	
// ================================ ADDITIONAL FUNCTIONS SECTION =============================================
	
	// Last Week of the year
	function getLastWeek($year) {
		$date = new DateTime;
		$date->setISODate($year, 53);
		return ($date->format("W") === "53" ? 53 : 52);
	}
	
	// To check if database table history has value
	function checkHistoryData(){
		$con = $GLOBALS['con'];
		$query = "SELECT * FROM t_history";
		$result = $con->query($query);
		if(mysqli_num_rows($result)>0){
		  return true;
		} else {
		  return false;
		}
	}
 ?>
