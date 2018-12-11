<?php
  require_once 'Config.php';
  //This is for database population
  if(isset($_GET['printSQL'])){
    echo "INSERT INTO `t_history` (`uid`, `consumed`, `effective_date`, `lst_updt_dte`) VALUES";
    for ($j=11; $j <=11; $j++) {
      $consumption = rand(0.2,0.3);
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
		echo "<pre>"; echo "('6f63b28', ".$consumption.", '2018-".$j."-".$k." 9:30:39', '2018-09-28 08:54:39')";
		echo ",";
		$consumption+=0.15;
      }
    }
    echo ";";
  }

?>
