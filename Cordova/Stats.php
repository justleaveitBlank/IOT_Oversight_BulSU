<?php
  require_once 'Config.php';

  if(isset($_POST['getdaily'])){
    $consumptionarray = array();
    $hoursarray = array();
    $id = $_POST['uid'];
    $date = ($_POST['getdaily']=="now")? "NOW()" : "'".$_POST['getdaily']."'";

    //MinHours
    $select = "IFNULL(MIN(HOUR(effective_date)),(HOUR(NOW()))-8) as MinHours , IFNULL(MAX(HOUR(effective_date)),HOUR(NOW())) as MaxHours";
    $table = "t_history";
    $where = "DATE(effective_date) = DATE(".$date.")";
    $result = processQuery($select,$table,$where);

    if(mysqli_num_rows($result)>0){
      while($row = mysqli_fetch_assoc($result)){
        $MinTime = intval($row['MinHours']);
        $MaxTime = intval($row['MaxHours']);
      }
    } else {
      print mysqli_error($con);
    }

    for ($i=$MinTime; $i <=$MaxTime; $i++) {
      array_push($hoursarray,$i.":00");
      array_push($consumptionarray,0);
    }

    //Retrieve Consumption
    $apps = getConsumers(date("Y-m-d h:i:sa"),"MONTH");
    for ($h=0; $h < sizeof($apps) ; $h++) {
      $id = $apps[$h]['uid'];
      $x = 0;
      for ($i=$MinTime; $i <=$MaxTime; $i++) {
        $select = "IFNULL(MAX(consumed), (SELECT IFNULL(MAX(th.consumed),0) from t_history as th where uid='$id' and  date(th.effective_date) = date(".$date.") and HOUR(th.effective_date) < $i)) as  consumed";
        $table = "t_history";
        $where = "uid='$id' and  DATE(effective_date)=DATE(".$date.") and HOUR(effective_date) = ".$i;
        $result = processQuery($select,$table,$where);

        if(mysqli_num_rows($result)>0){
          while($row = mysqli_fetch_assoc($result)){
            $consumptionarray[$x] += intval($row["consumed"]);
            $x++;
          }
        } else {
          print mysqli_error($con);
        }
      }
    }
    echo json_encode($hoursarray)."|".json_encode($consumptionarray);
  }

  if(isset($_POST['getweekly'])){
    $consumptionarray = array();
    $daysarray = array("SUN","MON","TUE","WED","THU","FRI","SAT");
    $id = $_POST['uid'];
    $day = intval($_POST['Day']);
    $getdate = ($_POST['getweekly']=="now")? date("Y-m-d") : $_POST['getweekly'];
    //get how many days from sunday and then subtract to getdate to get sunday's date
    $newday = $day - 1;
    $date = date('Y-m-d', strtotime($getdate. ' - ' . $newday . ' days'));
    $apps = getConsumers($date,"MONTH");
    $finalarray = array(0,0,0,0,0,0,0);
    for ($h=0; $h < sizeof($apps) ; $h++) {
      $id = $apps[$h]['uid'];
      $consumptionarray = array();
      for ($i=0; $i < 7; $i++) {
        $curdate = date('Y-m-d', strtotime($date. ' + ' . $i . ' days'));
        $select = "IFNULL(MAX(consumed), (SELECT IFNULL(MAX(th.consumed),0) from t_history as th where uid='$id' and  YEAR(th.effective_date) = YEAR('".$curdate."') and MONTH(th.effective_date) = MONTH('".$curdate."') and DAY(th.effective_date) < DAY('".$curdate."'))) as  consumed";
        $table = "t_history";
        $where = "uid='$id' and  DATE(effective_date) = DATE('".$curdate."')";
        $result = processQuery($select,$table,$where);

        if(mysqli_num_rows($result)>0){
          while($row = mysqli_fetch_assoc($result)){
            $finalarray[$i] += intval($row["consumed"]);
          }
        } else {
          print mysqli_error($con);
        }
      }
    }
    echo json_encode($daysarray)."|".json_encode($finalarray);
  }

  if(isset($_POST['getmonthly'])){
    $finalarray = array(0,0,0,0,0,0,0,0,0,0,0,0);
    $monthsarray = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
    $id = $_POST['uid'];
    $date = ($_POST['getmonthly']=="now")? date("Y-m-d") : $_POST['getmonthly'];

    $apps = getConsumers($date,"MONTH");
    for ($h=0; $h < sizeof($apps) ; $h++) {
      $id = $apps[$h]['uid'];
      $consumptionarray = array();
      for ($i=1; $i <= 12; $i++) {
        $select = "IFNULL(MAX(consumed), 0) as  consumed";
        $table = "t_history";
        $where = "uid='$id' and  YEAR(effective_date) = YEAR('".$date."') and MONTH(effective_date) = " . $i;
        $result = processQuery($select,$table,$where);

        if(mysqli_num_rows($result)>0){
          while($row = mysqli_fetch_assoc($result)){
            array_push($consumptionarray,intval($row["consumed"]));
          }
        } else {
          print mysqli_error($con);
        }
      }
      for ($j=0; $j < sizeof($finalarray); $j++) {
        $finalarray[$j] = $finalarray[$j] + $consumptionarray[$j];
      }
    }

    echo json_encode($monthsarray)."|".json_encode($finalarray);
  }

  if(isset($_POST['getyearly'])){
    $id = $_POST['uid'];
    $date = ($_POST['getyearly']=="now")? date("Y-m-d") : $_POST['getyearly'];
    $thedate = explode('-',$date);
    $maxdate = explode('-',date("Y-m-d"));
    if($thedate[0]==$maxdate[0]){
      $thedate[0]=$maxdate[0] - 3;
    }
    $consumptionarray = array();
    $yearsarray = array();
    for ($j=$thedate[0]; $j <=$maxdate[0]; $j++) {
      array_push($yearsarray,$j);
      $query = "SELECT IFNULL(SUM(MAXVAL),0) as consumed FROM (SELECT  uid , MONTH(effective_date) AS DateVal, MAX(t.consumed) AS MaxVal
      FROM t_history AS t
      WHERE YEAR(t.effective_date) = $j
      GROUP BY MONTH(`effective_date`), uid) as historyview";

      $result = $con->query($query);

      if(mysqli_num_rows($result)>0){
        while($row = mysqli_fetch_assoc($result)){
          array_push($consumptionarray,$row["consumed"]);
        }
      } else {
        print mysqli_error($con);
      }
    }
    echo json_encode($yearsarray)."|".json_encode($consumptionarray);
  }

  if(isset($_POST['getconsumers'])){
    $selectedDate = $_POST['getconsumers'];
    $type = $_POST['type'];
    $datetime = ($selectedDate == "now")? date("Y-m-d") : $selectedDate;
    $result = getConsumers($datetime,$type);
    echo json_encode($result);
  }

  function getConsumers($datetime,$type){
    $con = $GLOBALS['con'];
    $rows = array();

    $query = "SELECT distinct(uid) FROM t_history WHERE " .$type. "(effective_date)=" .$type. "('".$datetime."')";
    $result = $con->query($query);
    if(mysqli_num_rows($result)>0){
      while($row = mysqli_fetch_assoc($result)){
        $rows[] = $row;
      }
      return $rows;
    }
  }
 ?>
