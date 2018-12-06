<?php
  require_once 'Config.php';
  
  if(isset($_POST['getweekly'])){
    $consumptionarray = array();
	$singleAppArray = array();
	$singleAppName = array();
    $daysarray = array("SUN","MON","TUE","WED","THU","FRI","SAT");
    $id = $_POST['uid'];
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
			$consumptionQuery = "SELECT IFNULL((SELECT consumed FROM t_history WHERE uid='".$currentUID."' and DATE(effective_date) = DATE('".$curdate."')),0) as consumed";
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

  if(isset($_POST['getmonthly'])){
    $finalarray = array();
	$singleAppArray = array();
    $monthsarray = array();
	$singleAppName = array();
    $id = $_POST['uid'];
    $date = ($_POST['getmonthly']=="now")? date("Y-m-d") : $_POST['getmonthly'];
	$min = 1;
    $min= ($_POST['getmonthly']=="now")? (int)date("d") : (int)date("d",strtotime($_POST['getmonthly']));


    $valid = checkHistoryData();
    if($valid){
		//------------------------------OVERALL MONTHLY--------------------------
		
		
      $consumptionarray = array();
      for ($i=$min; $i <= 30; $i++) {
		array_push($monthsarray,$i);
		array_push($finalarray,0);
        $select = "IFNULL(SUM(consumed), 0) as  consumed";
        $table = "t_history";
        $where = "YEAR(effective_date) = YEAR('".$date."') and MONTH(effective_date) = MONTH('".$date."') and DAY(effective_date) = " . $i;
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
			for ($i=$min; $i <=30; $i++) {
				$consumptionQuery = "SELECT IFNULL((SELECT IFNULL(SUM(consumed),0) FROM t_history WHERE uid='".$currentUID."' and YEAR('".$date."') and MONTH(effective_date) = MONTH('".$date."') and DAY(effective_date) = ".$i."),0) as consumed";
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
    $id = $_POST['uid'];
    $date = ($_POST['getyearly']=="now")? date("Y-m-d") : $_POST['getyearly'];
	$min = 1;
    $min= ($_POST['getyearly']=="now")? (int)date("m") : (int)date("m",strtotime($_POST['getyearly']));


    $valid = checkHistoryData();
    if($valid){
		//------------------------------OVERALL YEARLY--------------------------
      $consumptionarray = array();
      for ($i=$min; $i <= 12; $i++) {
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
			for ($i=$min; $i <=12; $i++) {
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

  if(isset($_POST['getconsumers'])){
    $selectedDate = $_POST['getconsumers'];
    $type = $_POST['type'];
    $datetime = ($selectedDate == "now")? date("Y-m-d") : $selectedDate;
    $result = getConsumers($datetime,$type);
    echo json_encode($result);
  }
  
  if(isset($_POST['applianceSummary'])){
	  $uid = $_POST['applianceSummary'];
	  $chartSet = $_POST['chartSet'];
	  $price = $_POST['price'];
	  $color = $_POST['color'];
	  $consumptions = json_decode($chartSet);
	  $sum = (array_sum($consumptions));
	  
	  $avg = $sum/count($consumptions);
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

  function getConsumers($datetime,$type){
    $con = $GLOBALS['con'];
    $rows = array();

    $query = "SELECT IFNULL(distinct(uid),0) as uid FROM t_history WHERE " .$type. "(effective_date)=" .$type. "('".$datetime."')";
    $result = $con->query($query);
    if(mysqli_num_rows($result)>0){
      while($row = mysqli_fetch_assoc($result)){
        $rows[] = $row;
      }
      return $rows;
    }
  }

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
