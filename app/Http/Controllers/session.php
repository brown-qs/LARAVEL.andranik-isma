<?php
include_once ('userinterface.php');
include_once ('databaseinterface.php');

  db_connect();
  
  set_time_limit (0);
  
  $id = $_POST['userID'];
//  file_put_contents("test.log",$id);

  while(true)
  {
    sleep(20);

    $query = "select timediff(concat(curdate(), ' ', curtime()), concat(date, ' ', time)) as duration
              from user_activity where user_id='".$id."' order by date desc, time desc limit 1";
    
    
//    file_put_contents("test.log",$id);
    $result = mysql_query($query);
    $row = mysql_fetch_object($result);                  
    if(!$row) {
      user_logout($id);
      return;
    }
    mysql_free_result($result);
    $time = explode(":",$row->duration);
    
    if($time[0] != 0 || $time[1] >= 10)
    {
      user_logout($id);
      return;
    }
  }

?>