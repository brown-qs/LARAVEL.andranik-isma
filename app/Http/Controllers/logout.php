<?php
  include_once ('userinterface.php');
  $id = $_POST['userID'];
//  file_put_contents("test.log",$id);
  user_logout($id);

?>