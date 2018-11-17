<?php
  //produce only once
  // if(($notifStat == true || $notifStat == "true") && $notifStat!="false" ){
    //echo "notifStat - [" . $notifStat ."] ";
    //echo "aDevice - [" . $aDevice ."] ";
    //create json object
    $appliance_pluggedStatus = new stdClass();
    //initialize json array object
    $appliance_pluggedStatus->plugged = $aDevice;
    $appliance_pluggedStatus->uid = "";
    $appliance_pluggedStatus->registered = false;
    // adevice = 1 if there is appliiance plugged
   // echo $aDevice . $num;
    if($aDevice == "1"){
      //check if plugged device is registered then set id and register status
      $applianceQuery = "SELECT * FROM t_appliance WHERE appl_name NOT IN ('Unregistered_Appliance','Anonymous_Appliance') AND uid='".$UID."'";
      $applianceResult = $con->query($applianceQuery);
      $numROWS = mysqli_num_rows($applianceResult);
      if($numROWS==1){
        $appliance_pluggedStatus->uid = $appl_uid;
        $appliance_pluggedStatus->registered = true;
        //remove any unregistered and anonymous appliance in t_appliance and t_notifications ( for rfid issues )

        $query = "DELETE FROM t_appliance WHERE appl_name in ('Unregistered_Appliance','Anonymous_Appliance')";
      	$con->query($query);

        //remove any unregistered and anonymous appliance in t_notifications ( for rfid issues )
  			$Notifquery = "UPDATE t_notification SET status='ignored' WHERE type IN ('newanoapp','newapp') and status='unresolved'";
  			$con->query($Notifquery);

      } else {
        $appliance_pluggedStatus->uid = $appl_uid;
        $appliance_pluggedStatus->registered = false;
      }
    }
    //convert json to string and create json file
    $json_has_power_data = json_encode($appliance_pluggedStatus, JSON_PRETTY_PRINT);
    file_put_contents('plugged.json',  $json_has_power_data);
  // }

  //adevice = 2 if notification is ignored but still plugged
  if ($aDevice == "2"){
    $appliance_pluggedStatus = array(
      "plugged"=>"2",
      "uid"=>$appl_uid,
      "registered"=>false
    );
    $json_has_power_data = json_encode($appliance_pluggedStatus, JSON_PRETTY_PRINT);
    file_put_contents('plugged.json',  $json_has_power_data);
  }

  //adevice = 3 if device is allowed and still plugged
  if ($aDevice == "3"){
    $appliance_pluggedStatus = array(
      "plugged"=>"1",
      "uid"=>$appl_uid,
      "registered"=>true
    );
    $json_has_power_data = json_encode($appliance_pluggedStatus, JSON_PRETTY_PRINT);
    file_put_contents('plugged.json',  $json_has_power_data);
  }

  //adevice = 0 if device is unplugged
  if ($aDevice == "0" || $unPlugged=="true"){
	//delete anonymous and unregistered appliance once unplugged appliance
	$query = "DELETE FROM t_appliance WHERE appl_name in ('Unregistered_Appliance','Anonymous_Appliance')";
	$con->query($query);

    $query = "UPDATE t_appliance SET time_limit_value = NOW() WHERE time_limit_value = '0000-00-00 00:00:00'";
    if($con->query($query)){
      //Remove Notices or Notificatons to avoid Confusion ( Notification will occur only when there is plugged devices )
      $Notifquery = "UPDATE t_notification SET status='ignored' WHERE status='unresolved'";
      if($con->query($Notifquery)){
        //Set value 0 in plugged devices to denote no appliance is currently plugged in
        $appliance_pluggedStatus = array(
          "plugged"=>"0",
          "uid"=>"",
          "registered"=>false
        );
        $json_has_power_data = json_encode($appliance_pluggedStatus, JSON_PRETTY_PRINT);
        file_put_contents('plugged.json',  $json_has_power_data);
      } else {
        echo mysqli_error($con);
      }
    } else {
      echo mysqli_error($con);
    }

  }
?>
