<?php
  require 'Config.php';

  if(isset($_GET['getallstats'])){
    $data = $_GET['getallstats'];
    $arr = array();
    $query = "SELECT uid from t_appliance";
    $curhour = date('H');
    $id = '6f63b28';

    for ($hour=0; $hour <= $curhour; $hour++) {
      if($data == "now"){
        $select = "IFNULL(MAX(consumed), (SELECT IFNULL(MAX(th.consumed),0) from t_history as th where uid='$id' and  date(th.effective_date) = date(NOW()) and HOUR(th.effective_date) < $hour)) as  consumed";
        $table = "t_history";
        $where = "uid='$id' and  DATE(effective_date) and HOUR(effective_date) = ".$hour;
        $result = processQuery($select,$table,$where);

        if(mysqli_num_rows($result)>0){
          while($row = mysqli_fetch_assoc($result)){
            echo $row["consumed"]; echo " ";
          }
        } else {
          print mysqli_error($con);
        }
      }
    }
  }

 ?>
