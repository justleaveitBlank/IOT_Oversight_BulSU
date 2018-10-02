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
    $where = "uid='".$id."' and DATE(effective_date) = DATE(".$date.")";
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
    }

    //Retrieve Consumption
    for ($i=$MinTime; $i <=$MaxTime; $i++) {
      $select = "IFNULL(MAX(consumed), (SELECT IFNULL(MAX(th.consumed),0) from t_history as th where uid='$id' and  date(th.effective_date) = date(".$date.") and HOUR(th.effective_date) < $i)) as  consumed";
      $table = "t_history";
      $where = "uid='$id' and  DATE(effective_date)=DATE(".$date.") and HOUR(effective_date) = ".$i;
      $result = processQuery($select,$table,$where);

      if(mysqli_num_rows($result)>0){
        while($row = mysqli_fetch_assoc($result)){
          array_push($consumptionarray,intval($row["consumed"]));
        }
      } else {
        print mysqli_error($con);
      }
    }
    echo json_encode($hoursarray)."|".json_encode($consumptionarray);
  }

  if(isset($_POST['getweekly'])){
    $consumptionarray = array();
    $daysarray = array();
    $daysarray = array("SUN","MON","TUE","WED","THU","FRI","SAT");
    $id = $_POST['uid'];
    $day = intval($_POST['Day']);
    $getdate = ($_POST['getweekly']=="now")? date("Y-m-d") : $_POST['getweekly'];
    //get how many days from sunday and then subtract to getdate to get sunday's date
    $newday = $day - 1;
    $date = date('Y-m-d', strtotime($getdate. ' - ' . $newday . ' days'));
    for ($i=0; $i < 7; $i++) {
      $curdate = date('Y-m-d', strtotime($date. ' + ' . $i . ' days'));
      $select = "IFNULL(MAX(consumed), (SELECT IFNULL(MAX(th.consumed),0) from t_history as th where uid='$id' and  YEAR(th.effective_date) = YEAR('".$curdate."') and MONTH(th.effective_date) = MONTH('".$curdate."') and DAY(th.effective_date) < DAY('".$curdate."'))) as  consumed";
      $table = "t_history";
      $where = "uid='$id' and  DATE(effective_date) = DATE('".$curdate."')";
      $result = processQuery($select,$table,$where);

      if(mysqli_num_rows($result)>0){
        while($row = mysqli_fetch_assoc($result)){
          array_push($consumptionarray,intval($row["consumed"]));
        }
      } else {
        print mysqli_error($con);
      }
    }
    echo json_encode($daysarray)."|".json_encode($consumptionarray);
  }

  if(isset($_POST['getmonthly'])){
    $consumptionarray = array();
    $monthsarray = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
    $id = $_POST['uid'];
    $date = ($_POST['getmonthly']=="now")? date("Y-m-d") : $_POST['getmonthly'];

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
    echo json_encode($monthsarray)."|".json_encode($consumptionarray);
  }

  if(isset($_POST['getyearly'])){
    $consumptionarray = array();
    $yearsarray = array();
    $id = $_POST['uid'];
    $date = ($_POST['getyearly']=="now")? date("Y-m-d") : $_POST['getyearly'];
    $thedate = explode('-',$date);
    $maxdate = explode('-',date("Y-m-d"));
    if($thedate[0]==$maxdate[0]){
      $thedate[0]=$maxdate[0] - 3;
    }

    for ($i=$thedate[0]; $i <=$maxdate[0]; $i++) {
      array_push($yearsarray,$i);
      $select = "IFNULL(MAX(consumed), 0) as  consumed";
      $table = "t_history";
      $where = "uid='$id' and  YEAR(effective_date) = " . $i;
      $result = processQuery($select,$table,$where);

      if(mysqli_num_rows($result)>0){
        while($row = mysqli_fetch_assoc($result)){
          array_push($consumptionarray,intval($row["consumed"]));
        }
      } else {
        print mysqli_error($con);
      }
    }
    echo json_encode($yearsarray)."|".json_encode($consumptionarray);
  }

 ?>
