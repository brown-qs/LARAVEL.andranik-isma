<?php
include_once ('userinterface.php');
include_once ("databaseinterface.php");


class userservices
{
    function userLogin($userName, $password, $defLang)
    {
      return  user_login($userName, $password, $defLang);
    }
    function userLogout($id)
    {
      return user_logout($id);
    }

    function getUserInfo($userID)
    {
      return get_user_info($userID);
    }
    function saveUserInfo($user)
    {
      return save_user_info($user);
    }
    function removeUser($userID)
    {
      return remove_user($userID);
    }
    function getUserTask($id, $inbox)
    {
      return get_user_task($id, $inbox);
    }
    function saveUserTask($id, $coll, $inbox)
    {
      return save_user_task($id, $coll, $inbox);
    }
    function saveTime($id, $date, $duration)
    {
      return save_time($id, $date, $duration);
    }
    function getTime($id, $year, $month, $day)
    {
      return get_time($id, $year, $month, $day);
    }


    function createTest($name, $direct)
    {
      return create_test($name, $direct);
    }
    function deleteTest($id)
    {
      return delete_test($id);
    }
    function getTest()
    {
      return get_test();
    }


    function getTestContent($id)
    {
      return get_test_content($id);
    }
    function saveTestContent($id, $tc)
    {
      return save_test_content($id, $tc);
    }
    function test($id)
    {
      return test_content($id);
    }

}
?>
