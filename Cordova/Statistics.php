<?php
  require_once 'Config.php';
  //This is for database population
  if(isset($_GET['printSQL'])){
    echo "INSERT INTO `t_history` (`uid`, `consumed`, `effective_date`, `lst_updt_dte`) VALUES";
    for ($j=1; $j <=12; $j++) {
      $consumption = rand(2,3);
      $month31 = array(1,3,5,7,8,10,12);
      $month30 = array(4,6,9,11);
      if (in_array($j,$month31)){
        $maxday = 31;
      } else if (in_array($j,$month30)){
        $maxday = 30;
      } else {
        $maxday = 28;
      }
      for ($k=1; $k <= $maxday ; $k++) {
        for ($i=8; $i <=15 ; $i++) {
          if(rand(0,1) == 1){
            echo "<pre>"; echo "('6f63b28', ".$consumption.", '2016-".$j."-".$k." ".$i.":30:39', '2016-09-28 08:54:39')";
            echo ",";
            $consumption++;
          }
        }
      }
    }
    echo ";";
  }

?>
