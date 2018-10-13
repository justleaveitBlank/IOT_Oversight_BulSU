<?php
  //produce only once
  if($notifStat == true && $notifStat!="false"){
    //echo "notifStat - [" . $notifStat ."] ";
    //echo "aDevice - [" . $aDevice ."] ";
    //create json object
    $appliance_pluggedStatus = new stdClass();
    //initialize json array object
    $appliance_pluggedStatus->plugged = $aDevice;
    $appliance_pluggedStatus->uid = "";
    $appliance_pluggedStatus->registered = false;
    //check if there is plugged
    if($aDevice == "1"){
      //check if plugged device is registered
      if($num>0){
        $appliance_pluggedStatus->uid = $appl_uid;
        $appliance_pluggedStatus->registered = true;
      } else if ($num<=0){
        $appliance_pluggedStatus->uid = $appl_uid;
      }
    }
    //convert json to string and create json file
    $json_has_power_data = json_encode($appliance_pluggedStatus, JSON_PRETTY_PRINT);
    file_put_contents('plugged.json',  $json_has_power_data);
  }

  //if notification is resolved but still plugged
  if ($aDevice == "3"){
    $appliance_pluggedStatus = array(
      "plugged"=>"1",
      "uid"=>$appl_uid,
      "registered"=>true
    );
    $json_has_power_data = json_encode($appliance_pluggedStatus, JSON_PRETTY_PRINT);
    file_put_contents('plugged.json',  $json_has_power_data);
  }

  if ($aDevice == "2"){
    $appliance_pluggedStatus = array(
      "plugged"=>"2",
      "uid"=>$appl_uid,
      "registered"=>false
    );
    $json_has_power_data = json_encode($appliance_pluggedStatus, JSON_PRETTY_PRINT);
    file_put_contents('plugged.json',  $json_has_power_data);
  }

  //if device is unplugged
  if ($aDevice == "0"){
    $appliance_pluggedStatus = array(
      "plugged"=>"0",
      "uid"=>"",
      "registered"=>false
    );
    $json_has_power_data = json_encode($appliance_pluggedStatus, JSON_PRETTY_PRINT);
    file_put_contents('plugged.json',  $json_has_power_data);
  }
?>
