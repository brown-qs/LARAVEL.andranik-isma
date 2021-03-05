<?php
include_once ('databaseinterface.php');

define("PERM_ARM", 1);
define("PERM_ENG", 2);
define("PERM_WAM", 4);
define("PERM_RUS", 8);
define("PERM_CAPTION", 256);
define("PERM_REL", 512);
define("PERM_PROG", 1024);
define("PERM_DESC", 2048);
define("PERM_DEFORM", 4096);
define("PERM_SYNTAX", 8192);
define("PERM_MATRIX", 16384);
define("PERM_TASK",32768);
define("PERM_ADMIN",65536);
define("PERM_TRANS", 131072);




function user_login($userName, $password, $defLang)
{
  // session_start();
  $_SESSION['def_lang'] = $defLang;
  
  db_connect();
  $query = "select id, permission, rw_permission from users where user_name='".$userName."' and password='".$password."'";
  $result = mysql_query($query);
  $id = mysql_fetch_object($result);
  
  $tmp = new User();
  $tmp->id = 0;
  if(!$id) return $tmp;
  $tmp = new User();
  $tmp->id = $id->id;
  $tmp->permission = $id->permission;  
  $tmp->rwPermission = $id->rw_permission;
  
  mysql_free_result($result);

  $query = "select user_id from user_activity where user_id='".$id->id."' limit 1";

  $result = mysql_query($query);
  $row = mysql_fetch_object($result);
  mysql_free_result($result);

  if(!$row)
  {
    $insert_query = "insert into user_log values ('".$id->id."',CURDATE(),CURTIME(),0,1,0,0)";
    mysql_query($insert_query);
    $insert_query = "insert into user_activity values ('".$id->id."',CURDATE(),CURTIME(),0,1,0,0)";
    mysql_query($insert_query);
  }
  else
  {
    $delete_query = "delete from user_activity where user_id='".$id->id."' and log_type!='1'";
    mysql_query($delete_query);
  }
  return $tmp;
}

function add_times($t1, $t2)
{
  $vek1 = explode(":",$t1);
  $vek2 = explode(":",$t2);

  $sum1 = $vek1[2] + $vek2[2];
  $add = 0;
  if($sum1 >= 60) 
  { $sum1 -= 60;
    $add = 1;
  }

  $sum2 = $vek1[1] + $vek2[1] + $add;
  $add = 0;
  if($sum2 >= 60) 
  { $sum2 -= 60;
    $add = 1;
  }
  $sum3 = $vek1[0] + $vek2[0] + $add;
  return $sum3.":".$sum2.":".$sum1;
}

function user_logout($id)
{
  db_connect();

  $insert_query = "insert into user_log values ('".$id."',CURDATE(),CURTIME(),0,2,0,0)";
  mysql_query($insert_query);

  // calculating time duration for current user
  $query = "select timediff(concat(curdate(), ' ', curtime()), concat(date, ' ', time)) as duration, date
            from user_activity where user_id='".$id."' and log_type='1'";

  
  $result = mysql_query($query);
  $row = mysql_fetch_object($result);                  
  mysql_free_result($result);
  if(!$row) return;

  $delete_query = "delete from user_activity where user_id='".$id."'";
  mysql_query($delete_query);

  $new_duration = $row->duration;
  $query = "select duration from time_table where user_id = '".$id."' and date = '".$row->date."' and source = '1'";

//  file_put_contents("test.log",$query);
  
  $result = mysql_query($query);
  $new_row = mysql_fetch_object($result);                  
  if($new_row)
  {
    $new_duration = add_times($new_duration, $new_row->duration);
    ///file_put_contents("test.log",$new_duration);

  
    $delete_query = "delete from time_table where user_id='".$id."' and date='".$row->date."' and source = '1'";
    mysql_query($delete_query);
  }

  mysql_free_result($result);

  $insert_query = "insert into time_table values ('".$id."','".$row->date."','".$new_duration."','1')";
  mysql_query($insert_query);
}


function get_user_info($userID) 
{
  db_connect();

  if($userID == 0)
    $query = "select * from users";
  else
    $query = "select * from users where id='".$userID."'";

  $result = mysql_query($query);
  while ($row = mysql_fetch_object($result)) 
  {
    $tmp = new User();

    $tmp->id = $row->id;
    $tmp->permission = $row->permission;  
    $tmp->rwPermission = $row->rw_permission;
    $tmp->userName = $row->user_name;    
    $tmp->password = $row->password;    
    $tmp->fName = $row->first_name; 
    $tmp->lName = $row->last_name; 
    $tmp->mName = $row->middle_name; 
    $tmp->email = $row->email; 
    $tmp->phone = $row->phone;
    $tmp->cPhone = $row->cell_phone;
    $tmp->address = $row->address;   
    $ret[] = $tmp;
  }
  mysql_free_result($result);
  return $ret;

}

function save_user_info($user)
{
  db_connect();

  $query = "select id from users where user_name='".$user['userName']."'";
//  file_put_contents("test.log",$user['userID']);

  $result = mysql_query($query);
  $id = mysql_fetch_object($result);
  mysql_free_result($result);
  if($id && ($id->id != $user['id'])) 
  {
    return "The user with the name '".$user['userName']."' already exists in database, choose another name!";
  }

  if($user['id'] == 0)
  { $query = "select max(id) as max from users";
    $result = mysql_query($query);
    $buff = mysql_fetch_object($result);
    $user['id'] = $buff->max+1;
    mysql_free_result($result);
  }
  else
  {
    $delete_query = "delete from users where id = '".$user['id']."'";
    mysql_query($delete_query);
  }

  $insert_query = "insert into users values ('".$user['id']."','".$user['userName']."','".$user['password']."','".$user['fName']."','".$user['lName'].
                    "','".$user['mName']."','".$user['email']."','".$user['phone']."','".$user['cPhone']."','".$user['permission']."','".$user['rwPermission']."','".$user['address']."')";
  mysql_query($insert_query);
  return "";
}

function remove_user($userID)
{
  db_connect();

  $delete_query = "delete from users where id = '".$userID."'";
  mysql_query($delete_query);

  $delete_query = "delete from tasks where id = '".$userID."' or from_id = '".$userID."'";
  mysql_query($delete_query);

  $delete_query = "delete from time_table where user_id = '".$userID."'";
  mysql_query($delete_query);
  
  $delete_query = "delete from user_log where user_id = '".$userID."'";
  mysql_query($delete_query);

  $delete_query = "delete from user_activity where user_id = '".$userID."'";
  mysql_query($delete_query);
}

function get_user_task($id, $inbox)
{
  db_connect();
  if($inbox == true)
    $query = "select t.id as id, t.from_id as from_id, t.date as taskDate, t.time as taskTime, t.task as task, u1.user_name as currUserName, u2.user_name as userName 
              from tasks as t left join users as u1 on t.id = u1.id left join users as u2 on t.from_id = u2.id
              where t.id='".$id."' order by taskDate desc, taskTime desc";
  else
    $query = "select t.id as id, t.from_id as from_id, t.date as taskDate, t.time as taskTime, t.task as task, u1.user_name as currUserName, u2.user_name as userName 
              from tasks as t left join users as u1 on t.id = u1.id left join users as u2 on t.from_id = u2.id
              where t.from_id='".$id."' order by taskDate desc, taskTime desc";

  //file_put_contents("test.log",$query);
  $result = mysql_query($query);
  while($row = mysql_fetch_object($result)) 
  {
    $tmp = new UserTask();

    $tmp->id = $row->id;
    $tmp->currUserName = $row->currUserName;
    $tmp->from_id = $row->from_id;
    $tmp->userName = $row->userName;
    $tmp->taskDate = $row->taskDate;
    $tmp->taskTime = $row->taskTime;
    $tmp->task = $row->task;
    $ret[] = $tmp;
  }
  mysql_free_result($result);
  return $ret;
}

function  save_user_task($id, $coll, $inbox)
{
  db_connect();

  if($inbox == true)
    $delete_query = "delete from tasks where id = '".$id."'";
  else
    $delete_query = "delete from tasks where from_id = '".$id."'";
  mysql_query($delete_query);
  //file_put_contents("test.log",$insert_query);

  $insert_query = "insert into tasks values ";
  $count = count($coll);
  for($i = 0; $i < $count; $i++)
  { $date = $coll[$i]['taskDate'];
    $time = $coll[$i]['taskTime'];

    $coll[$i]['task'] = str_replace("'", "\'", $coll[$i]['task']);
    $coll[$i]['task'] = str_replace('"', '\"', $coll[$i]['task']);

    if(!strlen($coll[$i]['taskDate']) || !strlen($coll[$i]['taskTime']))
      $insert_query = $insert_query."('".$coll[$i]['id']."','".$coll[$i]['from_id']."',CURDATE(),CURTIME(),'".$coll[$i]['task']."'),";
    else
      $insert_query = $insert_query."('".$coll[$i]['id']."','".$coll[$i]['from_id']."','".$date."','".$time."','".$coll[$i]['task']."'),";
  }

  $insert_query = substr($insert_query,0,strlen($insert_query) - 1);
  //file_put_contents("test.log",$insert_query);
  mysql_query($insert_query);
}

function save_time($id, $date, $duration)
{
  db_connect();

  $delete_query = "delete from time_table where user_id = '".$id."' and date = '".$date."' and source = '2'";
  mysql_query($delete_query);

  //file_put_contents("test.log",$delete_query);
  $insert_query = "insert into time_table values ('".$id."','".$date."','".$duration."','2')";
  mysql_query($insert_query);
}

function get_time($id, $year, $month, $day)
{
  db_connect();
  if(strlen($day))
  {
    $date = $year."-".$month."-".$day;
    $query = "select t1.date as date, t1.user_id as id, t1.duration as duration, t2.duration as customDuration 
              from time_table as t1 left join time_table as t2 on t1.user_id = t2.user_id and t1.date = t2.date and t1.source = '1' and t2.source = '2'
              where t1.date = '".$date."' and t1.user_id = '".$id."' and t1.source = '1'";
  }
  else
    $query = "select t1.date as date, t1.user_id as id, t1.duration as duration, t2.duration as customDuration 
              from time_table as t1 left join time_table as t2 on t1.user_id = t2.user_id and t1.date = t2.date and t1.source = '1' and t2.source = '2'
              where year(t1.date) = '".$year."' and month(t1.date) = '".$month."' and t1.user_id = '".$id."' and t1.source = '1'";

  $result = mysql_query($query);
  while ($row = mysql_fetch_object($result)) 
  {
    $tmp = new UserTime();

    $tmp->id = $row->id;
    $tmp->date = $row->date;
    $tmp->duration = $row->duration;
    $tmp->customDuration = $row->customDuration;
    $ret[] = $tmp;
  }
  mysql_free_result($result);
  return $ret;
}

function create_test($name, $direct)
{
  db_connect();
  $query = "insert into test(name, trans_direction) values ('{$name}', '{$direct}')";
  mysql_query($query);
}

function delete_test($id)
{
  db_connect();
  $query = "delete from test where id='{$id}'";
  mysql_query($query);
  $query = "delete from test_content where test_id='{$id}'";
  mysql_query($query);
}

function get_test()
{
  db_connect();

  $query = "select id, name, trans_direction from test";


  $result = mysql_query($query);
  while ($row = mysql_fetch_object($result)) 
  {
    $tmp = new Test();

    $tmp->id = $row->id;
    $tmp->name = $row->name;
    $tmp->transDirection = $row->trans_direction;
    $ret[] = $tmp;
  }
  mysql_free_result($result);
  return $ret;
}
function get_test_content($id)
{
  db_connect();

  $query = "select test_id, num, src_text, dst_text from test_content where test_id='{$id}' order by num";


  $result = mysql_query($query);
  if(!$result)  return $ret;
  while ($row = mysql_fetch_object($result)) 
  {
    $tmp = new TestContent();

    $tmp->test_id = $row->test_id;
    $tmp->num = $row->num;
    $tmp->src_text = utf8_encode($row->src_text);
    $tmp->dst_text = utf8_encode($row->dst_text);
    $ret[] = $tmp;
  }
  mysql_free_result($result);
  return $ret;
}

function save_test_content($id, $tc)
{
  db_connect();
  $query = "delete from test_content where test_id='{$id}'";
  mysql_query($query);
  if(count($tc) == 0) return;
  $query = "insert into test_content values ";
  for($i = 0; $i < count($tc); $i++)
  {
    $tc[$i]['src_text'] = str_replace("'", "\'", $tc[$i]['src_text']);
    $tc[$i]['src_text'] = str_replace('"', '\"', $tc[$i]['src_text']);
    $tc[$i]['dst_text'] = str_replace("'", "\'", $tc[$i]['dst_text']);
    $tc[$i]['dst_text'] = str_replace('"', '\"', $tc[$i]['dst_text']);

    if($i != count($tc) - 1)
      $query = $query."('{$tc[$i]['test_id']}','".$tc[$i]['num']."','".utf8_decode($tc[$i]['src_text'])."','".utf8_decode($tc[$i]['dst_text'])."'),";
    else
      $query = $query."('{$tc[$i]['test_id']}','".$tc[$i]['num']."','".utf8_decode($tc[$i]['src_text'])."','".utf8_decode($tc[$i]['dst_text'])."')";
  }
  mysql_query($query);
}

function test_content($id)
{
  db_connect();

  $user_name = log_store(1000, 5, 3, 0, 0);
  if(strlen($user_name)!=0) // if some user is now converting the base
    return "The user '{$user_name}', is now updating the data base, please try test in a few seconds";
  else
    close_data(1000, 5, 0, 0);

  $query = "select trans_direction from test where id='{$id}'";
  $result = mysql_query($query);
  $row = mysql_fetch_object($result);
  return send_translation_request($id, $row->trans_direction, "test");
} 
?>