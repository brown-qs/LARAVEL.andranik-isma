<?php

use Illuminate\Support\Facades\Session;

include_once ('concept.php');
include_once ('datatest.php');
include_once("includes/socket/my_socket.php");
include_once("includes/string/string.php");


define("DATABASE_SERVER", "localhost");
define("DATABASE_USERNAME", "root");
define("DATABASE_PASSWORD", "root12");


define("DATABASE_NAME", "knowbot");
define("TRANSSERVER_HOST", "109.75.37.241");
define("TRANSSERVER_PORT", 4044);
define("CONVSERVER_HOST", "109.75.37.241");
define("CONVSERVER_PORT", 7070);
define("SOURCE_BINARY", "/home/Converter/");
define("DESTINAION_BINARY", "/home/wet/");

define("IMAGES_URL", "http://109.75.37.241:8111/Editor/amfphp/services/images/");
define("IMAGES_PATH", "images/");

/*define( "DATABASE_SERVER", "localhost");
define( "DATABASE_USERNAME", "root");
define( "DATABASE_PASSWORD", "");
define( "DATABASE_NAME", "knowbot1");
define("TRANSSERVER_HOST", "10.100.2.214"); 
define("TRANSSERVER_PORT", 4040); 
define("CONVSERVER_HOST", "10.100.2.214"); 
define("CONVSERVER_PORT", 7070); 
define("SOURCE_BINARY", "/data/Converter/");
define("DESTINAION_BINARY", "/data/wet/");
*/

function mysql_query($query)
{
  return mysqli_query(Session::get('db_link'), $query);
}
function mysql_fetch_object($result)
{
  return mysqli_fetch_object($result);
}
function mysql_free_result($result)
{
  return mysqli_free_result($result);
}

function db_connect()
{
  $link = mysqli_connect(env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'))
    or die('Not connected : ' . mysqli_error($link));
  mysqli_select_db($link, env('DB_DATABASE'));
  Session::put('db_link', $link);
  return $link;
}


function check($mask, $lang, $type = 0, $section = 0, $isConcept = false)
{
  //db_connect();
  if (($mask & MATRIX) || ($mask & SYNTAX)) {
    $query = "select id from schedule where sub_id=$lang and mask=$mask";
    $result = mysql_query($query);
    $row = mysql_fetch_object($result);
    if (!$row) {
      $shedule_id = uniqid("");
      mysql_query("insert into schedule(id, sub_id, mask) values('$shedule_id', $lang, $mask)");
    }
    mysql_free_result($result);
  }

  if (($mask & DEFORM_INDEXING) || ($mask & DEFORM)) {

    $query = "select id from schedule where sub_id=$type and mask=$mask and param=$lang";
    $result = mysql_query($query);
    $row = mysql_fetch_object($result);
    if (!$row) {
      $shedule_id = uniqid("");
      mysql_query("insert into schedule(id, sub_id, mask, param) values('$shedule_id', $type, $mask, $lang)");
    }
    mysql_free_result($result);
  }
  if ($mask & UPDATE_ROOTS || $mask & DELETE_ROOTS) {
    $query = "select id from schedule where sub_id=$section and mask=$mask and param=$lang and text_param=$type";
    //file_put_contents("test.log","select id from schedule where sub_id=$section and mask=$mask and param=$lang and text_param=$type");
    $result = mysql_query($query);
    $row = mysql_fetch_object($result);
    if (!$row) {
      $shedule_id = uniqid("");
      mysql_query("insert into schedule(id, sub_id, mask, param, text_param) values('$shedule_id', $section, $mask, $lang, '$type')");
      //file_put_contents("test.log","insert into schedule(id, sub_id, mask, param, text_param) values('$shedule_id', $section, $mask, $lang, '$type')");
    }
    mysql_free_result($result);
  }
  if ($mask & ADD_ELEMENTS) {
    $query = "select id from schedule where  mask=$mask and param=$lang";
    $result = mysql_query($query);
    $row = mysql_fetch_object($result);
    if (!$row) {
      $shedule_id = uniqid("");
      mysql_query("insert into schedule(id, mask, param) values('$shedule_id', $mask, $lang)");
    }
    mysql_free_result($result);
  }
  if ($isConcept) {
    $conceptMask = 0;
    $conceptMask = $conceptMask | ARM | ENG | WAM | CAPTION | REL | MASK_CLASS | ENV | PROG;


    $query = "select mask from schedule where  sub_id=$lang and mask&$conceptMask<>0";
    $result = mysql_query($query);
    $row = mysql_fetch_object($result);
    if (!$row) {
      $shedule_id = uniqid("");
      mysql_query("insert into schedule(id, mask, sub_id) values('$shedule_id', $mask, $lang)");
      //      file_put_contents("test.log","insert into schedule(id, mask, param) values($shedule_id, $mask, $lang)");
    } else {
      if (($row->mask & $mask) != $mask)
        mysql_query("update schedule set mask=mask|{$mask}  where sub_id={$lang} and mask={$row->mask}");
    }

    mysql_free_result($result);
  }
}

// this function returns true if such log exists in table for another user
/*
  $dataType values
  1 - concept, 2 - deform, 3 - syntax, 4 - matrix, 5 - convert all data

  $logType values
  1 - login, 2 - logout, 3 - get, 4 - save, 5 - delete, 6 - add, 7 - close
*/
function log_store($userId, $dataType, $logType, $p1, $p2)
{
  //db_connect();

  $insert_query = "insert into user_log values ('" . $userId . "',CURDATE(),CURTIME(),'" . $dataType . "','" . $logType . "','" . $p1 . "','" . $p2 . "')";
  mysql_query($insert_query);
  if ($logType == 3) // get
  {
    $query = "select user_name
              from user_activity left join users on user_id = id
              where user_id<>'" . $userId . "' and data_type = '" . $dataType . "' and log_type = '" . $logType . "' and param1 = '" . $p1 . "' and param2 = '" . $p2 . "'";
    //    file_put_contents("test.log",$query);

    //   file_put_contents("test.log",$query);

    $result = mysql_query($query);
    $row = mysql_fetch_object($result);
    mysql_free_result($result);
    if (!$row) {
      $insert_query = "insert into user_activity values ('" . $userId . "',CURDATE(),CURTIME(),'" . $dataType . "','" . $logType . "','" . $p1 . "','" . $p2 . "')";
      mysql_query($insert_query);
      return "";
    }
    return $row->user_name;
  }
  return "";
}

function close_data($userId, $dataType, $p1, $p2)
{
  log_store($userId, $dataType, 7, $p1, $p2);
  $delete_query = "delete from user_activity 
                   where user_id='" . $userId . "' and data_type = '" . $dataType . "' and param1 = '" . $p1 . "' and param2 = '" . $p2 . "' limit 1";
  ///file_put_contents("test.log",$delete_query);
  mysql_query($delete_query);
}



function search_prog_comment($word, $lang, $progType, $progStage, $progText, $commText)
{
  $ret = array();

  if (strlen($word) == 0 && $progType == 0 && $progStage == "ANY" && strlen($progText) == 0 && strlen($commText) == 0)
    return $ret;

  if ($lang < 7) {
    $word = utf8_decode($word);
  }
  $word = str_replace("'", "\'", $word);
  $word = str_replace('"', '\"', $word);
  $progText = utf8_decode($progText);
  $progText = str_replace("'", "\'", $progText);
  $progText = str_replace('"', '\"', $progText);
  $commText = utf8_decode($commText);
  $commText = str_replace("'", "\'", $commText);
  $commText = str_replace('"', '\"', $commText);

  db_connect();
  $query = "select w.id, w.synonym_num, w.word, w.roots, w.lang  
            from words as w left outer join programs as p on w.id = p.id
                            left outer join comments as c on w.id = c.id
            where 
            ";
  if (strlen($word) != 0)
    $query = $query . "w.word='" . $word . "' and ";
  if ($lang != 0)
    $query = $query . "p.language='" . $lang . "' and w.lang='" . $lang . "' and ";
  if ($progType != 0)
    $query = $query . "p.prog_type='" . $progType . "' and ";
  if ($progStage != "ANY")
    $query = $query . "p.stage='" . $progStage . "' and ";
  if (strlen($progText) != 0)
    $query = $query . "p.program like '%" . $progText . "%' and ";
  if (strlen($commText) != 0)
    $query = $query . "c.comment like '%" . $commText . "%' and ";


  $query = substr($query, 0, strlen($query) - 4);
  $query = $query . " group by w.id";

  $result = mysql_query($query);
  while ($row = mysql_fetch_object($result)) {
    $tmp = new SearchResult();
    $tmp->id = $row->id;
    $tmp->syn = $row->synonym_num;
    $tmp->roots = utf8_encode($row->roots);
    $tmp->word = utf8_encode($row->word);
    $tmp->lang = $row->lang;
    $ret[] = $tmp;
  }
  mysql_free_result($result);
  return $ret;
}



function search_by_name($sp)
{
  $ret = array();
  // session_start();
  $defLang = $_SESSION['def_lang'];

  db_connect();
  if (strlen($sp['id']) != 0) {
    $new_id = mix_id(1, $sp['id']);
    $query = "select w.id, w.synonym_num, w.word, w.roots, w.lang from words as w where w.id = '" . $new_id . "' and w.lang = {$defLang}";
    $result = mysql_query($query);

    while ($row = mysql_fetch_object($result)) {
      $tmp = new SearchResult();
      $tmp->id = $row->id;
      $tmp->syn = $row->synonym_num;
      $tmp->roots = utf8_encode($row->roots);
      $tmp->word = utf8_encode($row->word);
      $tmp->lang = $row->lang;
      $ret[] = $tmp;
    }
    mysql_free_result($result);
    return $ret;
  }

  if (
    strlen($sp['baseRole']) == 0 && strlen($sp['frequency']) == 0 && count($sp['relations']) == 0 && strlen($sp['className']) == 0
    && strlen($sp['envName']) == 0 && strlen($sp['text']) == 0 && strlen($sp['rootNumber']) == 0 && strlen($sp['synNumber']) == 0
    && strlen($sp['role']) == 0 && strlen($sp['defNumber']) == 0
  )
    return $ret;

  // and w.synonym_num = arm_w.synonym_num   
  $query = "select w.id, w.synonym_num, w.word, w.roots, w.lang, arm_w.word as arm_word, arm_w.roots as arm_roots
            from words as w left outer join words as arm_w on w.id=arm_w.id and arm_w.lang = {$defLang} and arm_w.synonym_num = '1' ";
  if (strlen($sp['baseRole']) != 0 || strlen($sp['frequency']) != 0)
    $query = $query . "left outer join caption as c on w.id = c.id ";

  $rel_count = count($sp['relations']);
  if ($rel_count != 0)
    $query = $query . "left outer join relation as r on w.id=r.id 
                     left outer join words as rel_w on (rel_w.id = r.rel_id and rel_w.lang = {$defLang} and  rel_w.synonym_num = 1) ";


  if (strlen($sp['className']) != 0) {
    //    $sp['className'] = utf8_decode($sp['className']);
    if ($sp['classInClasses'] != true)
      $query = $query . "left outer join class as cls on cls.id = w.id 
                       left outer join words as cls_w on cls_w.id = cls.class_id and cls_w.synonym_num = 1 and cls_w.lang = {$defLang} ";
    else
      $query = $query . "left outer join class as cls1 on cls1.class_id = w.id 
                       left outer join words as cls1_w on cls1_w.id = cls1.id  and cls_w1.synonym_num = 1 and cls_w1.lang = {$defLang} ";
  }

  if (strlen($sp['envName']) != 0) {
    //    $sp['envName'] = utf8_decode($sp['envName']);
    if ($sp['envInEnvs'] != true)
      $query = $query . "left outer join environment as env on env.id = w.id 
                       left outer join words as env_w on env_w.id = env.env_id and env_w.synonym_num = 1 and env_w.lang = {$defLang} ";
    else
      $query = $query . "left outer join environment as env1 on env1.env_id = w.id 
                       left outer join words as env1_w on env1_w.id = env1.id and env1_w.synonym_num = 1 and env1_w.lang = {$defLang} ";
  }
  $query = $query . "where ";


  if ($sp['language'] < 7) {
    $sp['text'] = utf8_decode($sp['text']);
  }
  $sp['text'] = str_replace("'", "\'", $sp['text']);
  $sp['text'] = str_replace('"', '\"', $sp['text']);

  $prefix = false;
  $suffix = false;
  if (strlen($sp['text']) != 0) {

    if ($sp['searchInRoots'] == false && $sp['searchAsSubtext'] == false) {
      if ($sp['text'][0] == '#') {
        $prefix = true;
        $sp['text'] = substr($sp['text'], 1, strlen($sp['text']) - 1);
      }

      if ($sp['text'][strlen($sp['text']) - 1] == '#') {
        $suffix = true;
        $sp['text'] = substr($sp['text'], 0, strlen($sp['text']) - 1);
      }
      $query = $query . "w.word like '%" . $sp['text'] . "%' and ";
    }
    if ($sp['searchInRoots'] == true && $sp['searchAsSubtext'] == false)
      $query = $query . "w.roots='" . $sp['text'] . "' and ";
    if ($sp['searchInRoots'] == false && $sp['searchAsSubtext'] == true)
      $query = $query . "w.word like '%" . $sp['text'] . "%' and ";
    if ($sp['searchInRoots'] == true && $sp['searchAsSubtext'] == true)
      $query = $query . "w.roots like '%" . $sp['text'] . "%' and ";
  }
  if ($sp['language'] != 0)
    $query = $query . "w.lang = '" . $sp['language'] . "' and ";

  if (strlen($sp['rootNumber']) != 0) {
    if ($sp['rootNumberInv'] == false)
      $query = $query . "(length(w.roots) - length(replace(w.roots, ',', ''))='" . $sp['rootNumber'] . "') and ";
    else
      $query = $query . "(length(w.roots) - length(replace(w.roots, ',', ''))!='" . $sp['rootNumber'] . "') and ";
  }
  if (strlen($sp['baseRole']) != 0) {
    if ($sp['baseRoleInv'] == false)
      $query = $query . "c.parent_type='" . $sp['baseRole'] . "' and ";
    else
      $query = $query . "c.parent_type!='" . $sp['baseRole'] . "' and ";
  }
  if (strlen($sp['frequency']) != 0) {
    if ($sp['frequencyInv'] == false)
      $query = $query . "c.frequency='" . $sp['frequency'] . "' and ";
    else
      $query = $query . "c.frequency!='" . $sp['frequency'] . "' and ";
  }
  if (strlen($sp['synNumber']) != 0) {
    if ($sp['synNumberInv'] == false)
      $query = $query . "w.synonym_num='" . $sp['synNumber'] . "' and ";
    else
      $query = $query . "w.synonym_num!='" . $sp['synNumber'] . "' and ";
  }

  if (strlen($sp['role']) != 0 && strlen($sp['defNumber']) == 0) {
    if ($sp['roleInv'] == false)
      $query = $query . "(w.role1='" . $sp['role'] . "' or w.role2='" . $sp['role'] . "' or w.role3='" . $sp['role'] . "' or w.role4='" . $sp['role'] . "' or w.role5='" . $sp['role'] . "') and ";
    else
      $query = $query . "(w.role1!='" . $sp['role'] . "' and w.role2!='" . $sp['role'] . "' and w.role3!='" . $sp['role'] . "' and w.role4!='" . $sp['role'] . "' and w.role5!='" . $sp['role'] . "') and ";
  }

  if (strlen($sp['defNumber']) != 0 && strlen($sp['role']) == 0) {
    if ($sp['defNumberInv'] == false)
      $query = $query . "(w.decl1='" . $sp['defNumber'] . "' or w.decl2='" . $sp['defNumber'] . "' or w.decl3='" . $sp['defNumber'] . "' or w.decl4='" . $sp['defNumber'] . "' or w.decl5='" . $sp['defNumber'] . "') and ";
    else
      $query = $query . "(w.decl1!='" . $sp['defNumber'] . "' and w.decl2!='" . $sp['defNumber'] . "' and w.decl3!='" . $sp['defNumber'] . "' and w.decl4!='" . $sp['defNumber'] . "' and w.decl5!='" . $sp['defNumber'] . "') and ";
  }

  if (strlen($sp['role']) != 0 && strlen($sp['defNumber']) != 0) {
    if ($sp['defNumberInv'] == true && $sp['roleInv'] == true)
      $query = $query . "( (w.role1!='" . $sp['role'] . "' or w.decl1!='" . $sp['defNumber'] . "') and
      			 (w.role2!='" . $sp['role'] . "' or w.decl2!='" . $sp['defNumber'] . "') and	
      			 (w.role3!='" . $sp['role'] . "' or w.decl3!='" . $sp['defNumber'] . "') and	
      			 (w.role4!='" . $sp['role'] . "' or w.decl4!='" . $sp['defNumber'] . "') and	
      			 (w.role5!='" . $sp['role'] . "' or w.decl5!='" . $sp['defNumber'] . "') ) and ";
    else
      $query = $query . "( (w.role1='" . $sp['role'] . "' and w.decl1='" . $sp['defNumber'] . "') or
      			 (w.role2='" . $sp['role'] . "' and w.decl2='" . $sp['defNumber'] . "') or	
      			 (w.role3='" . $sp['role'] . "' and w.decl3='" . $sp['defNumber'] . "') or	
      			 (w.role4='" . $sp['role'] . "' and w.decl4='" . $sp['defNumber'] . "') or	
      			 (w.role5='" . $sp['role'] . "' and w.decl5='" . $sp['defNumber'] . "') ) and ";
  }

  if ($rel_count != 0) {
    $first_loop = false;
    for ($i = 0; $i < $rel_count; $i++) {
      $rel = $sp['relations'][$i];
      $rel['conceptName'] = utf8_decode($rel['conceptName']);

      //      file_put_contents("c:\\2.txt", $rel['conceptName']);
      if (strlen($rel['code1']) == 0 && strlen($rel['code2']) == 0 && strlen($rel['conceptName']) == 0 && strlen($rel['prob']) == 0)
        continue;
      if ($first_loop == false) {
        $query = $query . "((";
        $first_loop = true;
      } else {
        if ($rel['andor'] == true)
          $query = $query . " and (";
        else
          $query = $query . " or (";
      }
      if (strlen($rel['code1']) == 0)
        $query = $query . "r.relation_type!='168' and ";
      if (strlen($rel['code1']) != 0 && $rel['inv'] == false)
        $query = $query . "r.relation_type='" . $rel['code1'] . "' and ";
      if (strlen($rel['code1']) != 0 && $rel['inv'] == true)
        $query = $query . "r.relation_type!='" . $rel['code1'] . "' or ";
      if (strlen($rel['code2']) != 0 && $rel['inv'] == false)
        $query = $query . "r.relation_subtype='" . $rel['code2'] . "' and ";
      if (strlen($rel['code2']) != 0 && $rel['inv'] == true)
        $query = $query . "r.relation_subtype!='" . $rel['code2'] . "' or ";
      if (strlen($rel['prob']) != 0 && $rel['inv'] == false)
        $query = $query . "r.probability='" . $rel['prob'] . "' and ";
      if (strlen($rel['prob']) != 0 && $rel['inv'] == true)
        $query = $query . "r.probability!='" . $rel['prob'] . "' or ";
      if (strlen($rel['conceptName']) != 0 && $rel['inv'] == false)
        $query = $query . "rel_w.word='" . $rel['conceptName'] . "' and ";
      if (strlen($rel['conceptName']) != 0 && $rel['inv'] == true)
        $query = $query . "rel_w.word!='" . $rel['conceptName'] . "' or ";
      $query = substr($query, 0, strlen($query) - 4);

      $query = $query . ")     ";
    }
    if ($first_loop != false)
      $query = $query . ") and ";
  }

  if (strlen($sp['className']) != 0) {
    $sp['className'] = utf8_decode($sp['className']);
    if ($sp['classInClasses'] == true) {
      if (strlen($sp['classDist']) == 0)
        $query = $query . "cls1_w.word='" . $sp['className'] . "' and ";
      else
        $query = $query . "(cls1_w.word='" . $sp['className'] . "' and cls1.distance='" . $sp['classDist'] . "') and ";
    } else {
      if ($sp['classNameInv'] == false) {
        if (strlen($sp['classDist']) == 0)
          $query = $query . "cls_w.word='" . $sp['className'] . "' and ";
        else
          $query = $query . "(cls_w.word='" . $sp['className'] . "' and cls.distance='" . $sp['classDist'] . "') and ";
      } else {
        if (strlen($sp['classDist']) == 0)
          $query = $query . "cls_w.word!='" . $sp['className'] . "' and ";
        else
          $query = $query . "(cls_w.word!='" . $sp['className'] . "' and cls.distance='" . $sp['classDist'] . "') and ";
      }
    }
  }
  if (strlen($sp['envName']) != 0) {
    $sp['envName'] = utf8_decode($sp['envName']);
    if ($sp['envInEnvs'] == true) {
      $query = $query . "env1_w.word='" . $sp['envName'] . "' and ";
    } else {
      if ($sp['envNameInv'] == false)
        $query = $query . "env_w.word='" . $sp['envName'] . "' and ";
      else
        $query = $query . "env_w.word!='" . $sp['envName'] . "' and ";
    }
  }

  $query = substr($query, 0, strlen($query) - 4);
  //  $query = $query." group by w.id";

  $result = mysql_query($query);
  file_put_contents("test.log", $query);

  while ($row = mysql_fetch_object($result)) {
    $tmp = new SearchResult();

    $fl = false;
    for ($i = 0; $i < count($ret); $i++)
      if ($ret[$i]->id == $row->id) {
        $fl = true;
        break;
      }
    if ($fl) continue;

    if (strlen($sp['text']) != 0 && $sp['searchInRoots'] == false && $sp['searchAsSubtext'] == false) {
      $foundWord = $row->word;
      $sp['text'] = str_replace("\'", "'", $sp['text']);
      $sp['text'] = str_replace('\"', '"', $sp['text']);


      //    file_put_contents("test.log", $row->word." -- ".$sp['text']);
      //      break;
      if ($foundWord[0] == '(') {
        for ($i = 1; $i < strlen($foundWord); $i++)
          if ($foundWord[$i] == ')')
            break;
        if ($i != strlen($foundWord))
          $foundWord = substr($foundWord, $i + 1, strlen($foundWord) - $i - 1);
      }

      if (!$prefix) {
        $fl = false;
        if (!$suffix && !$prefix && strlen($foundWord) != strlen($sp['text'])) {
          continue;
        }
        for ($i = 0; $i < strlen($foundWord); $i++) {
          if ($suffix && strlen($sp['text']) == $i)
            break;
          if ($sp['text'][$i] != $foundWord[$i]) {
            $fl = true;
            break;
          }
        }
        if ($fl) continue;
      }
      if (!$suffix && $prefix) {
        $fl = false;
        for ($i = strlen($foundWord) - 1; $i >= 0; $i--) {
          if ($foundWord[$i] != $sp['text'][strlen($sp['text']) - (strlen($foundWord) - $i)]) {
            $fl = true;
            break;
          }
          if (strlen($sp['text']) == strlen($foundWord) - $i)
            break;
        }
        if ($fl) continue;
      }
    }



    $tmp->id = $row->id;
    $tmp->syn = $row->synonym_num;
    $tmp->roots = utf8_encode($row->arm_roots);
    $tmp->word = utf8_encode($row->arm_word);
    $tmp->lang = $row->lang;
    $ret[] = $tmp;
  }
  mysql_free_result($result);
  return $ret;
}

function get_programs($id)
{
  db_connect();
  $ret = array();
  $query = "SELECT language, id, prog_num, prog_type, synonym, stage, program FROM programs WHERE id = '" . $id . "' order by prog_num";
  $result = mysql_query($query);

  while ($row = mysql_fetch_object($result)) {
    $tmp = new Program();
    $tmp->id = $row->id;
    $tmp->prog_num = $row->prog_num;
    $tmp->lang = $row->language;
    $tmp->progType = $row->prog_type;
    $tmp->stage = $row->stage;
    $tmp->syn = $row->synonym;
    $tmp->progText = utf8_encode(prog_ids_to_words($row->program));
    $ret[] = $tmp;
  }
  mysql_free_result($result);
  return $ret;
}

function get_relations($id)
{
  session_start();
  $defLang = $_SESSION['def_lang'];
  db_connect();
  $ret = array();
  $query = "SELECT relation.id, relation.rel_num, relation.relation_type, relation.relation_subtype, relation.rel_id, relation.probability, words.word as rel_name  
            FROM relation, words 
            WHERE relation.id = '" . $id . "'AND words.id=relation.rel_id AND words.synonym_num=1 AND words.lang={$defLang} order by relation.rel_num";

  $result = mysql_query($query);

  $ret = array();
  while ($row = mysql_fetch_object($result)) {
    $tmp = new Relation();
    $tmp->id = $row->id;
    $tmp->rel_num = $row->rel_num;
    $tmp->code1 = $row->relation_type;
    $tmp->code2 = $row->relation_subtype;
    $tmp->conceptID = $row->rel_id;
    $tmp->conceptName = utf8_encode($row->rel_name);
    $tmp->prob = $row->probability;
    $ret[] = $tmp;
  }
  mysql_free_result($result);
  return $ret;
}

function get_description($id)
{
  db_connect();
  $query = "SELECT comment FROM comments WHERE id = '" . $id . "'";
  $result = mysql_query($query);
  $row = mysql_fetch_object($result);
  $ret = utf8_encode($row->comment);
  mysql_free_result($result);
  return $ret;
}

function get_help($lang, $helpType)
{
  db_connect();
  $ret = "";
  $query = "SELECT help_text FROM help WHERE lang='{$lang}' and help_type='{$helpType}'";
  $result = mysql_query($query);
  $row = mysql_fetch_object($result);
  if ($row)
    $ret = utf8_encode($row->help_text);

  mysql_free_result($result);
  return $ret;
}

function save_help($lang, $helpType, $helpText)
{
  db_connect();

  $helpText = utf8_decode($helpText);
  $helpText = str_replace("'", "\'", $helpText);
  $helpText = str_replace('"', '\"', $helpText);

  $query = "delete from help WHERE lang='{$lang}' and help_type='{$helpType}'";
  mysql_query($query);
  $query = "insert into help values ('" . $lang . "','" . $helpType . "','" . $helpText . "')";
  mysql_query($query);
}



function get_caption($id)
{
  db_connect();
  $query = "SELECT id, parent_type, frequency FROM caption WHERE id = '" . $id . "'";
  $result = mysql_query($query);
  $row = mysql_fetch_object($result);
  $ret = new ConceptCaption();
  $ret->id = $row->id;
  $ret->type = $row->parent_type;
  $ret->prob = $row->frequency;
  mysql_free_result($result);
  return $ret;
}

function get_classes($id)
{
  session_start();
  $defLang = $_SESSION['def_lang'];

  db_connect();
  $query = "SELECT words.word as class_name, class.probability, class.class_id
            FROM class, words 
            WHERE class.id = '" . $id . "' AND words.id = class.class_id AND words.synonym_num='1'  AND words.lang={$defLang} order by class.distance desc";
  $result = mysql_query($query);

  $ret = array();
  while ($row = mysql_fetch_object($result)) {
    $tmp = new ConceptClass();
    $tmp->conceptName = utf8_encode($row->class_name);
    $tmp->id = $row->class_id;
    $tmp->prob = $row->probability;
    $ret[] = $tmp;
  }
  mysql_free_result($result);
  return $ret;
}
function get_class_ids($id)
{
  db_connect();
  $query = "SELECT class_id, probability FROM class  WHERE class.id = '" . $id . "'";
  $result = mysql_query($query);

  $ret = array();
  while ($row = mysql_fetch_object($result)) {
    $tmp = new ConceptClass();
    $tmp->id = $row->class_id;
    $tmp->prob = $row->probability;
    $ret[] = $tmp;
  }
  mysql_free_result($result);
  return $ret;
}

function get_environments($id)
{

  session_start();
  $defLang = $_SESSION['def_lang'];
  db_connect();
  $query = "SELECT words.word as env_name, environment.probability, environment.env_id
            FROM environment, words 
            WHERE environment.id = '" . $id . "' AND words.id = environment.env_id AND words.synonym_num='1' and words.lang={$defLang} order by environment.probability desc";
  $result = mysql_query($query);

  $ret = array();
  while ($row = mysql_fetch_object($result)) {
    $tmp = new ConceptClass();
    $tmp->conceptName = utf8_encode($row->env_name);
    $tmp->id = $row->env_id;
    $tmp->prob = $row->probability;
    $ret[] = $tmp;
  }
  mysql_free_result($result);
  return $ret;
}


function get_concept($userId, $id)
{
  db_connect();
  $ret = array();
  $query = "SELECT lang, id, synonym_num, word, roots, role1, decl1, role2, decl2, role3, decl3, role4, decl4, role5, decl5, mix_type 
            FROM words 
            WHERE words.id = '" . $id . "' order by synonym_num";

  $result = mysql_query($query);
  $ret = new Concept;
  while ($row = mysql_fetch_object($result)) {
    $tmp = new Word();
    $tmp->id = $row->id;
    if ($row->lang < 7) {
      $tmp->word = utf8_encode($row->word);
      $tmp->roots = utf8_encode($row->roots);
    } else {
      $tmp->word = $row->word;
      $tmp->roots = $row->roots;
    }
    $tmp->syn = $row->synonym_num;
    $tmp->r1 = $row->role1;
    $tmp->d1 = $row->decl1;
    $tmp->r2 = $row->role2;
    $tmp->d2 = $row->decl2;
    $tmp->r3 = $row->role3;
    $tmp->d3 = $row->decl3;
    $tmp->r4 = $row->role4;
    $tmp->d4 = $row->decl4;
    $tmp->r5 = $row->role5;
    $tmp->d5 = $row->decl5;
    $tmp->mix = $row->mix_type;
    $tmp->param = 0;
    if ($row->lang == 1)
      $ret->armWords[] = $tmp;
    if ($row->lang == 2)
      $ret->engWords[] = $tmp;
    if ($row->lang == 3)
      $ret->wamWords[] = $tmp;
    if ($row->lang == 4)
      $ret->rusWords[] = $tmp;
    if ($row->lang == 5)
      $ret->oamWords[] = $tmp;
    if ($row->lang == 6)
      $ret->latWords[] = $tmp;
    if ($row->lang == 7) {
      //$tmp->word = utf8_decode($row->word);
      //$tmp->roots = utf8_decode($row->roots);

      ///$tmp->word = utf8_decode($row->word);
      ///$tmp->roots = utf8_decode($row->roots);
      $ret->talWords[] = $tmp;
    }
    if ($row->lang == 8)
      $ret->trkWords[] = $tmp;
    if ($row->lang == 9)
      $ret->gerWords[] = $tmp;
    if ($row->lang == 10)
      $ret->itlWords[] = $tmp;
    if ($row->lang == 11)
      $ret->fraWords[] = $tmp;
    if ($row->lang == 12)
      $ret->zazWords[] = $tmp;
    if ($row->lang == 13)
      $ret->hamWords[] = $tmp;
    if ($row->lang == 14)
      $ret->asrWords[] = $tmp;
    if ($row->lang == 15)
      $ret->lezWords[] = $tmp;
    if ($row->lang == 16)
      $ret->chrWords[] = $tmp;
    if ($row->lang == 17)
      $ret->lazWords[] = $tmp;
    if ($row->lang == 18)
      $ret->prsWords[] = $tmp;
    if ($row->lang == 19)
      $ret->spnWords[] = $tmp;
  }
  mysql_free_result($result);

  $ret->caption = get_caption($id);
  $ret->relations = get_relations($id);
  $ret->programs = get_programs($id);
  $ret->classes = get_classes($id);
  $ret->environments = get_environments($id);
  $ret->desc = get_description($id);
  //get_definition($id, $ret);

  $user_name = log_store($userId, 1, 3, $id, 0);
  $ret2 = new ReturnData2();
  $ret2->error_code = 0;
  $ret2->user_name = "";
  $ret2->data = $ret;
  if (strlen($user_name) != 0) {
    $ret2->error_code = 1;
    $ret2->user_name = $user_name;
  }
  return $ret2;
}
function executeWordsQuery($lang, $concept)
{
  $langWords = "armWords";
  if ($lang == 1) $langWords = "armWords";
  if ($lang == 2) $langWords = "engWords";
  if ($lang == 3) $langWords = "wamWords";
  if ($lang == 4) $langWords = "rusWords";
  if ($lang == 5) $langWords = "oamWords";
  if ($lang == 6) $langWords = "latWords";
  if ($lang == 7) $langWords = "talWords";
  if ($lang == 8) $langWords = "trkWords";
  if ($lang == 9) $langWords = "gerWords";
  if ($lang == 10) $langWords = "itlWords";
  if ($lang == 11) $langWords = "fraWords";
  if ($lang == 12) $langWords = "zazWords";
  if ($lang == 13) $langWords = "hamWords";
  if ($lang == 14) $langWords = "asrWords";
  if ($lang == 15) $langWords = "lezWords";
  if ($lang == 16) $langWords = "chrWords";
  if ($lang == 17) $langWords = "lazWords";
  if ($lang == 18) $langWords = "prsWords";
  if ($lang == 19) $langWords = "spnWords";

  $query = "delete from words where words.id='" . $concept['id'] . "' and words.lang='" . $lang . "'";
  if (mysql_query($query) === false) {
    return false;
  }
  $syn_count = count($concept[$langWords]);
  $query = "insert into words values ";
  $find = false;
  for ($i = 0; $i < $syn_count; $i++) {
    $syn = $concept[$langWords][$i];
    if ($lang < 7) {
      $syn['roots'] = utf8_decode($syn['roots']);
    }

    //    file_put_contents("test.log", $syn['word']." --- ".utf8_decode($syn['word']));

    if ($lang < 7) {
      $syn['word'] = utf8_decode($syn['word']);
    }
    if ($syn['id'] == 0)
      $syn['id'] = $concept['id'];


    $roots = str_replace("'", "\'", $syn['roots']);
    $roots = str_replace('"', '\"', $roots);
    $word = str_replace("'", "\'", $syn['word']);
    $word = str_replace('"', '\"', $word);
    $query = $query . "('" . $lang . "','" . $syn['id'] . "','" . $syn['syn'] . "','" . $word . "','" . $word . "','" . $roots . "','" . $syn['r1'] .
      "','" . $syn['d1'] . "','" . $syn['r2'] . "','" . $syn['d2'] . "','" . $syn['r3'] . "','" . $syn['d3'] .
      "','" . $syn['r4'] . "','" . $syn['d4'] . "','" . $syn['r5'] . "','" . $syn['d5'] . "','" . $syn['mix'] . "'),";
    $find = true;
  }
  $query = substr($query, 0, strlen($query) - 1);
  if ($find === true && mysql_query($query) === false) {
    return false;
  }
  return true;
}

function send_translation_request($text, $trans_direction, $action = "translate")
{

  $socket = my_socket_connect(TRANSSERVER_HOST, TRANSSERVER_PORT);

  //  file_put_contents("test.log",TRANSSERVER_HOST." -- ".TRANSSERVER_PORT);

  $in_str = "action:{$action};time-out:" . $trans_socket_timeout . ";content-type:text;language:{$trans_direction};body-length:" . strlen($text) . ";\r\n" . $text;

  $in_str = GetIProto($in_str);

  if (my_socket_write_str($socket, $in_str) == false)
    return "";
  //  return "xxxx";
  $out_str = my_socket_read_nonblock($socket, 4096, 60, 0);

  if ($action == "stop")
    return "";

  $out_str = substr($out_str, 14);


  if ($out_str == false)
    return "";

  $trans_header_len = strpos($out_str, "\n");
  if ($trans_header_len == false)
    return "";

  $trans_header = substr($out_str, 0, $trans_header_len);
  if ($trans_header == false)
    return "";

  $TransText = substr($out_str, $trans_header_len + 1);
  //    file_put_contents("test.log",$out_str);
  if ($action == "test") {
    $ret = new TestResult;

    $pos1 = strpos($out_str, "error-count:");
    $pos2 = strpos($out_str, ";", $pos1);
    $ret->errorCount = substr($out_str, $pos1 + 12, $pos2 - $pos1 - 12);


    $pos1 = strpos($out_str, "test-time:");
    $pos2 = strpos($out_str, ";", $pos1);
    $ret->testTime = substr($out_str, $pos1 + 10, $pos2 - $pos1 - 10);
    $ret->errors = utf8_encode($TransText);


    return $ret;
  }
  return $TransText;
}

function send_converting_request($shedule_id, $content_type = "concept")
{
  $ret = new ReturnData();

  $ret->error_code = 0;
  $ret->error_string = "";

  $socket = my_socket_connect(CONVSERVER_HOST, CONVSERVER_PORT);


  if ($socket === false) {
    //file_put_contents("test.log","sock error");  
    $ret->error_code = 1;
    $ret->error_string = "Error <Server>: Unable connect to Converter Server\n";
    return $ret;
  }

  $in_str = "action:convert;content-type:" . $content_type . ";body-length:" . strlen($shedule_id) . ";\r\n" . $shedule_id;
  $in_str = GetIProto($in_str);

  if (my_socket_write_str($socket, $in_str) == false) {
    // file_put_contents("test.log","sock write  error");  
    $ret->error_code = 1;
    $ret->error_string = "Error <Server>: Unable send data to Converter Server\n";
    return $ret;
  }


  $out_str = my_socket_read_nonblock($socket, 4096, 60, 0);
  $out_str = substr($out_str, 14);
  //  file_put_contents("test.log",$out_str);

  if ($out_str == false) {
    $ret->error_code = 1;
    $ret->error_string = "Error <Server>: Answer header is empty\n";
    return $ret;
  }

  $trans_header_len = strpos($out_str, "\n");
  if ($trans_header_len == false) {
    $ret->error_code = 1;
    $ret->error_string = "Error <Server>: Slash n is not found in answer header \n";
    return $ret;
  }

  $trans_header = substr($out_str, 0, $trans_header_len);
  if ($trans_header == false) {
    $ret->error_code = 1;
    $ret->error_string = "Error <Server>: Wrong constructed answer header \n";
    return $ret;
  }

  $TransText = substr($out_str, $trans_header_len + 1);
  if (strlen($TransText) != 0) {
    $ret->error_code = 2;
    $ret->error_string = $TransText;
  }
  return $ret;
}


function save_data($userId, $concept)
{
  if ($concept['id'] == 0) {
    $ret = new ReturnData();

    $ret->error_code = 1;
    $ret->error_string = "Concept id is 0";
    return $ret;
  }


  $vol = 0;
  $id_num = split_id($concept['id'], $vol);

  if (($vol == 1 && $num >= 1 && $num <= 1000) || ($vol == 0 && $num >= 1 && $num <= 10)) {
    $ret = new ReturnData();
    $ret->error_code = 1;
    $ret->error_string = "No permissions to this concept";
    return $ret;
  }


  db_connect();
  mysql_query("START TRANSACTION");
  //mysql_query("lock tables words write, caption write, class write, environment write, relation write, definition write");

  $sendReqest = false;
  if ($concept['mask'] & CAPTION) {
    $sendReqest = true;
    $query = "delete from caption where caption.id='" . $concept['id'] . "'";
    mysql_query($query);
    $query = "insert into caption values ('" . $concept['id'] . "','" . $concept['caption']['type'] . "','" . $concept['caption']['prob'] . "')";
    if (mysql_query($query) === false) {
      mysql_query("ROLLBACK");
      mysql_query("unlock tables");
      $ret = new ReturnData();
      $ret->error_code = 1;
      $ret->error_string = "MySql internal error1";
      return $ret;
    }
  }

  if ($concept['mask'] & REL) {
    $sendReqest = true;
    $query = "delete from relation where relation.id='" . $concept['id'] . "'";
    if (mysql_query($query) === false) {
      mysql_query("ROLLBACK");
      mysql_query("unlock tables");
      $ret = new ReturnData();
      $ret->error_code = 1;
      $ret->error_string = "MySql internal error2";
      return $ret;
    }
    $rel_count = count($concept['relations']);

    $query = "insert into relation values ";
    $find_rel = false;
    for ($i = 0; $i < $rel_count; $i++) {
      $rel = $concept['relations'][$i];
      //           file_put_contents("test.log",$rel_count);

      if ($rel['id'] == 0) {
        $rel['id'] = $concept['id'];
        $rel['conceptID'] = get_concept_id(utf8_decode($rel['conceptName']));
        if ($rel['conceptID'] == 0 || $rel['conceptID'] === false) {
          continue;
        }

        if ($rel['code1'] == 157) {

          $query1 = "select count(*) as cnt from relation where rel_id='{$rel['id']}' and id ='{$rel['conceptID']}' and relation_type='29' and relation_subtype='{$rel['code2']}'";
          $result = mysql_query($query1);
          $count = mysql_fetch_object($result);
          mysql_free_result($result);
          if ($count->cnt == 0) {
            $query1 = "insert into relation values ('" . $rel['conceptID'] . "','" . $rel['rel_num'] . "','29','" . $rel['code2'] . "','" . $rel['id'] . "','" . $rel['prob'] . "')";
            if (mysql_query($query1) === false) {
              mysql_query("ROLLBACK");
              mysql_query("unlock tables");
              $ret = new ReturnData();
              $ret->error_code = 1;
              $ret->error_string = "MySql internal error3";
              return $ret;
            }
            $new_mask = 0;
            $new_mask = $new_mask | REL;
            check($new_mask, $rel['conceptID'], 0, 0, true);
          }
        }
      }
      if ($rel['id'] != $concept['id']) {
        continue;
      }
      $query = $query . "('" . $rel['id'] . "','" . $rel['rel_num'] . "','" . $rel['code1'] . "','" . $rel['code2'] . "','" . $rel['conceptID'] . "','" . $rel['prob'] . "'),";
      $find_rel = true;
    }
    $query = substr($query, 0, strlen($query) - 1);
    if ($find_rel === true && mysql_query($query) === false) {
      $ret = new ReturnData();
      mysql_query("ROLLBACK");
      mysql_query("unlock tables");
      $ret->error_code = 1;
      $ret->error_string = "MySql internal error4";
      return $ret;
    }
  }

  if ($concept['mask'] & PROG) {
    $sendReqest = true;
    $query = "delete from programs where programs.id='" . $concept['id'] . "'";
    mysql_query($query);
    $prog_count = count($concept['programs']);

    $query = "insert into programs values ";
    $find_program = false;
    for ($i = 0; $i < $prog_count; $i++) {

      $pr = $concept['programs'][$i];
      $pr['progText'] = utf8_decode($pr['progText']);

      if ($pr['id'] == 0)
        $pr['id'] = $concept['id'];

      $prog_text = $pr['progText'];
      prog_words_to_id($prog_text);
      $prog_text = str_replace("'", "\'", $prog_text);
      $prog_text = str_replace('"', '\"', $prog_text);
      //      if($i == 4)
      $query = $query . "('" . $pr['id'] . "','" . $pr['prog_num'] . "','" . $pr['lang'] . "','" . $pr['progType'] . "','" . $pr['stage'] . "','" . $pr['syn'] . "','" . $prog_text . "'),";
      //        if($i == 0)
      $find_program = true;
    }
    $query = substr($query, 0, strlen($query) - 1);


    if ($find_program === true && mysql_query($query) === false) {
      $ret = new ReturnData();
      mysql_query("ROLLBACK");
      mysql_query("unlock tables");
      $ret->error_code = 1;
      $ret->error_string = "MySql internal error5";
      return $ret;
    }
  }

  if (($concept['mask'] & MASK_CLASS == true) || ($concept['mask'] & ENV == true)) {
    $sendReqest = true;
  }


  if (
    $concept['mask'] & ARM || $concept['mask'] & ENG || $concept['mask'] & WAM || $concept['mask'] & RUS || $concept['mask'] & OAM || $concept['mask'] & LAT || $concept['mask'] & TAL || $concept['mask'] & TRK
    || $concept['mask'] & GER || $concept['mask'] & ITL || $concept['mask'] & FRA || $concept['mask'] & ZAZ || $concept['mask'] & HAM || $concept['mask'] & ASR || $concept['mask'] & LEZ || $concept['mask'] & CHR || $concept['mask'] & LAZ || $concept['mask'] & PRS || $concept['mask'] & SPN
  ) {
    $res = true;
    if ($concept['mask'] & ARM) {
      if (executeWordsQuery(1, $concept) == false) {
        $res = false;
      }
    }
    if ($concept['mask'] & ENG && $res === true) {
      if (executeWordsQuery(2, $concept) == false) {
        $res = false;
      }
    }
    if ($concept['mask'] & WAM && $res === true) {
      if (executeWordsQuery(3, $concept) == false) {
        $res = false;
      }
    }
    if ($concept['mask'] & RUS && $res === true) {
      if (executeWordsQuery(4, $concept) == false) {
        $res = false;
      }
    }
    if ($concept['mask'] & OAM && $res === true) {
      if (executeWordsQuery(5, $concept) == false) {
        $res = false;
      }
    }
    if ($concept['mask'] & LAT && $res === true) {
      if (executeWordsQuery(6, $concept) == false) {
        $res = false;
      }
    }
    if ($concept['mask'] & TAL && $res === true) {
      if (executeWordsQuery(7, $concept) == false) {
        $res = false;
      }
    }
    if ($concept['mask'] & TRK && $res === true) {
      if (executeWordsQuery(8, $concept) == false) {
        $res = false;
      }
    }
    if ($concept['mask'] & GER && $res === true) {
      if (executeWordsQuery(9, $concept) == false) {
        $res = false;
      }
    }
    if ($concept['mask'] & ITL && $res === true) {
      if (executeWordsQuery(10, $concept) == false) {
        $res = false;
      }
    }
    if ($concept['mask'] & FRA && $res === true) {
      if (executeWordsQuery(11, $concept) == false) {
        $res = false;
      }
    }
    if ($concept['mask'] & ZAZ && $res === true) {
      if (executeWordsQuery(12, $concept) == false) {
        $res = false;
      }
    }
    if ($concept['mask'] & HAM && $res === true) {
      if (executeWordsQuery(13, $concept) == false) {
        $res = false;
      }
    }
    if ($concept['mask'] & ASR && $res === true) {
      if (executeWordsQuery(14, $concept) == false) {
        $res = false;
      }
    }
    if ($concept['mask'] & LEZ && $res === true) {
      if (executeWordsQuery(15, $concept) == false) {
        $res = false;
      }
    }
    if ($concept['mask'] & CHR && $res === true) {
      if (executeWordsQuery(16, $concept) == false) {
        $res = false;
      }
    }
    if ($concept['mask'] & LAZ && $res === true) {
      if (executeWordsQuery(17, $concept) == false) {
        $res = false;
      }
    }
    if ($concept['mask'] & PRS && $res === true) {
      if (executeWordsQuery(18, $concept) == false) {
        $res = false;
      }
    }
    if ($concept['mask'] & SPN && $res === true) {
      if (executeWordsQuery(19, $concept) == false) {
        $res = false;
      }
    }
    if ($res === false) {
      $ret = new ReturnData();
      mysql_query("ROLLBACK");
      mysql_query("unlock tables");
      $ret->error_code = 1;
      $ret->error_string = "MySql internal error6";
      return $ret;
    }
    $sendReqest = true;
  }

  if ($concept['mask'] & DESC) {
    $query = "delete from comments where comments.id='" . $concept['id'] . "'";
    mysql_query($query);

    $concept['desc'] = utf8_decode($concept['desc']);

    $comment = str_replace("'", "\'", $concept['desc']);
    $comment = str_replace('"', '\"', $comment);
    $query = "insert into comments values ('" . $concept['id'] . "','" . $comment . "')";
    if (mysql_query($query) === false) {
      $ret = new ReturnData();
      mysql_query("ROLLBACK");
      mysql_query("unlock tables");
      $ret->error_code = 1;
      $ret->error_string = "MySql internal error7";
      return $ret;
    }
  }


  if ($concept['mask'] & DEFINITION) {
    $query = "delete from definition where id='" . $concept['id'] . "'";
    if (mysql_query($query) === false) {
      $ret = new ReturnData();
      mysql_query("ROLLBACK");
      mysql_query("unlock tables");
      $ret->error_code = 1;
      $ret->error_string = "MySql internal error8";
      return $ret;
    }
    $res = true;
    if (save_definition($concept['id'], 1, $concept['armDefinition']) === false) {
      $res = false;
    }
    if ($res === true && save_definition($concept['id'], 2, $concept['engDefinition']) === false) {
      $res = false;
    }
    if ($res === true && save_definition($concept['id'], 3, $concept['wamDefinition']) === false) {
      $res = false;
    }
    if ($res === true && save_definition($concept['id'], 4, $concept['rusDefinition']) === false) {
      $res = false;
    }
    if ($res === true && save_definition($concept['id'], 5, $concept['oamDefinition']) === false) {
      $res = false;
    }
    if ($res === true && save_definition($concept['id'], 6, $concept['latDefinition']) === false) {
      $res = false;
    }
    if ($res === false) {
      $ret = new ReturnData();
      mysql_query("ROLLBACK");
      mysql_query("unlock tables");
      $ret->error_code = 1;
      $ret->error_string = "MySql internal error9";
      return $ret;
    }
  }

  $ret = new ReturnData();

  $ret->error_code = 0;
  $ret->error_string = "";

  if ($sendReqest === true) {
    $new_mask = $concept['mask'];
    $new_mask = $new_mask & (~MASK_CLASS);
    $new_mask = $new_mask & (~ENV);
    $new_mask = $new_mask & (~CAPTION);
    $new_mask = $new_mask & (~REL);

    if ($new_mask)
      check($new_mask, $concept['id'], 0, 0, true);

    $new_mask = 0;
    if ($concept['mask'] & MASK_CLASS)
      $new_mask = $new_mask | ($concept['mask'] & MASK_CLASS);
    if ($concept['mask'] & ENV) {
      $new_mask = $new_mask | ($concept['mask'] & ENV);
    }
    if ($concept['mask'] & CAPTION)
      $new_mask = $new_mask | ($concept['mask'] & CAPTION);
    if ($concept['mask'] & REL) {
      $new_mask = $new_mask | ($concept['mask'] & REL);
      $new_mask = $new_mask | MASK_CLASS;
    }
    if ($new_mask) {
      $shedule_id = uniqid("");
      $query = "insert into schedule (id, sub_id, mask, param, text_param) values ('" . $shedule_id . "','" . $concept['id'] . "','" . $new_mask . "','0','')";
      if (mysql_query($query) === false) {
        $ret = new ReturnData();
        mysql_query("ROLLBACK");
        mysql_query("unlock tables");
        $ret->error_code = 1;
        $ret->error_string = "MySql internal error10 " . $query;
        return $ret;
      }

      mysql_query("COMMIT");
      $user_name = log_store($userId, 5, 3, 0, 0);
      if (strlen($user_name) == 0) // if some user is now converting the base
      {
        log_store($userId, 5, 4, 0, 0);

        $ret = send_converting_request($shedule_id);
        $ret->error_string = $ret->error_string;

        close_data($userId, 5, 0, 0);
      }
    }
  }

  $concept['classes'] = get_classes($concept['id']);
  //$ret->error_string = $ret->error_string.semantic_check($concept);


  $err_pos = strpos($ret->error_string, "Error");
  $warr_pos = strpos($ret->error_string, "Warning");
  if ($err_pos === false) {
    if ($warr_pos === FALSE);
    else
      $ret->error_code = 2;
  } else
    $ret->error_code = 1;
  $ret->error_string = utf8_encode($ret->error_string);


  if ($ret->error_code == 0) {
    log_store($userId, 1, 4, $concept['id'], 0);
    mysql_query("COMMIT");
  } else {
    mysql_query("ROLLBACK");
  }
  mysql_query("unlock tables");
  return $ret;
}

function delete_concept($userId, $id)
{
  db_connect();

  $ret = new ReturnData();

  $ret->error_code = 0;
  $ret->error_string = "";

  $query = "select id, relation_type, relation_subtype, rel_id, probability from relation where rel_id='" . $id . "'";


  $result = mysql_query($query);

  while (($row = mysql_fetch_object($result))) {
    $row = mysql_fetch_object($result);
    $ret = new ReturnData();
    $ret->error_code = 1;

    $concept_name = utf8_encode(get_concept_name($row->id));
    $rel_name = utf8_encode(get_concept_name($row->rel_num));
    $vol = 0;
    $id_num = split_id($row->id, $vol);


    $ret->error_string = "Error <Concept Delete>: Deleting concept is using in concept - '" . $concept_name . "' as " .
      $row->relation_type . "." . $row->relation_subtype . "=" . $rel_name . "," . $row->probability . " relation refferer\n";
    mysql_free_result($result);
    return $ret;
  }
  mysql_free_result($result);

  $vol = 0;
  $id_num = split_id($id, $vol);
  $new_id = $vol . "_" . $id_num;

  $query = "select id, language, prog_type, stage, synonym, program from programs where program like '%" . $new_id . "%'";
  $result = mysql_query($query);

  while ($row = mysql_fetch_object($result)) {

    $program = $row->program;

    $find = true;
    $pos = strpos($program, $new_id);
    if ($pos != 0) {
      $j = $pos - 1;
      if ($program[$j] == '0' || $program[$j] == '1' || $program[$j] == '2' || $program[$j] == '3' || $program[$j] == '4' || $program[$j] == '5' || $program[$j] == '6' || $program[$j] == '7' || $program[$j] == '8' || $program[$j] == '9') {
        $find = false;
      }
      $j = $pos + strlen($new_id);

      if ($program[$j] == '0' || $program[$j] == '1' || $program[$j] == '2' || $program[$j] == '3' || $program[$j] == '4' || $program[$j] == '5' || $program[$j] == '6' || $program[$j] == '7' || $program[$j] == '8' || $program[$j] == '9') {
        $find = false;
      }
    }
    if ($pos === false) {
      $find = false;
    }
    if ($find) {
      $ret->error_code = 1;

      $concept_name = utf8_encode(get_concept_name($row->id));

      $lang_name = get_lang_name($row->language);
      if ($row->prog_type == 1) $prog_type = "ANALYS";
      if ($row->prog_type == 2) $prog_type = "SYNTHESES";
      if ($row->prog_type == 3) $prog_type = "TREE";

      $ret->error_string = "Error <Concept Delete>: Deleting concept is in concept - '" . $concept_name . "' in program - '" . $lang_name . " " .
        $prog_type . " STAGE " . $row->stage . " " . " SYNONYM " . $row->synonym . "\n";
      mysql_free_result($result);
      return $ret;
    }
  }
  mysql_free_result($result);


  $query = "delete from caption where caption.id='" . $id . "'";
  mysql_query($query);

  $query = "delete from relation where relation.id='" . $id . "'";
  mysql_query($query);

  $query = "delete from programs where programs.id='" . $id . "'";
  mysql_query($query);

  $query = "delete from environment where environment.id='" . $id . "'";
  mysql_query($query);

  $query = "delete from class where class.id='" . $id . "'";
  mysql_query($query);

  $query = "delete from words where words.id='" . $id . "'";
  mysql_query($query);

  $query = "delete from comments where comments.id='" . $id . "'";
  mysql_query($query);


  $mask = 0;
  $mask = $mask | DELETE_CONCEPT;
  $shedule_id = uniqid("");
  $query = "insert into schedule (id, sub_id, mask, param, text_param) values ('" . $shedule_id . "','" . $id . "','" . $mask . "','0','')";
  mysql_query($query);

  $ret = send_converting_request($shedule_id);
  if ($ret->error_code == 0) {
    $query = "insert into deleted_concepts values ('" . $id . "')";
    mysql_query($query);
  }

  log_store($userId, 1, 5, $id, 0);
  return  $ret;
}

function create_concept($userId, $vol)
{
  db_connect();
  $mask = 0;
  $mask = $mask | ADD_CONCEPT;
  $shedule_id = uniqid("");
  $query = "insert into schedule (id, sub_id, mask, param, text_param) values ('" . $shedule_id . "','" . $vol . "','" . $mask . "','0','')";
  mysql_query($query);
  $ret = send_converting_request($shedule_id, "add_concept");

  log_store($userId, 1, 6, $ret->error_string, 0);
  log_store($userId, 1, 3, $ret->error_string, 0);

  return $ret->error_string;
}

function save_image_base64($conceptId, $lang, $imageBase64, $mathml)
{
  $fileContent = base64_decode($imageBase64);
  $imageBase64 = str_replace("'", "\'", $imageBase64);
  $imageBase64 = str_replace('"', '\"', $imageBase64);
  $srcMathml = $mathml;
  $mathml = str_replace("'", "\'", $mathml);
  $mathml = str_replace('"', '\"', $mathml);

  db_connect();
  $query = "insert into def_images(concept_id, lang, img_data, img_desc) values('{$conceptId}', '{$lang}', '{$imageBase64}', '{$mathml}')";
  mysql_query($query);


  $query = "select max(id) as id from def_images";
  $result = mysql_query($query);
  if ($result == false) {
    return "";
  }
  $row = mysql_fetch_object($result);
  $file_name = $row->id . ".png";
  mysql_free_result($result);
  $filePath = IMAGES_PATH . $file_name;

  if (file_exists($filePath) == false) {
    $f = fopen($filePath, "w");
    fclose($f);
  }


  file_put_contents($filePath, $fileContent);

  $ret['url'] = IMAGES_URL . $file_name;;
  $ret['desc'] = $srcMathml;
  return $ret;
}

function get_definition($id, $lang)
{
  db_connect();
  $query = "SELECT def FROM definition WHERE id = '{$id}' and lang='{$lang}'";
  $result = mysql_query($query);
  $row = mysql_fetch_object($result);
  if ($row != false) {
    if ($lang < 7) {
      $txt = utf8_encode($row->def);
    } else {
      $txt = $row->def;
    }
  }
  if (strpos($txt, "<flow:TextFlow ") === FALSE) {
    $txt = "<flow:TextFlow fontSize=\"14\" fontFamily=\"Arial Armenian\" whiteSpaceCollapse=\"preserve\" xmlns:flow=\"http://ns.adobe.com/textLayout/2008\"><flow:div><flow:p><flow:span>" . $txt . "</flow:span></flow:p></flow:div></flow:TextFlow>";
  }

  mysql_free_result($result);
  $pos = strpos($txt, "<flow:img ");
  while ($pos !== FALSE) {
    $srcPosStart = strpos($txt, "source=\"", $pos);
    if ($srcPosStart === FALSE)
      break;
    $srcPosStart += 8;
    $srcPosEnd = strpos($txt, "\"", $srcPosStart);
    if ($srcPosEnd === FALSE)
      break;
    $imgUrl = substr($txt, $srcPosStart, $srcPosEnd - $srcPosStart);

    $extPos = strpos($imgUrl, ".png");
    $slashPos = -1;
    for ($i = $extPos - 1; $i >= 0; $i--) {
      if ($imgUrl[$i] == "/") {
        $slashPos = $i;
        break;
      }
    }
    if ($slashPos == -1)
      break;
    $imdID = substr($imgUrl, $slashPos + 1, $extPos - $slashPos - 1);

    //file_put_contents("test.log",$imdID);
    $query = "SELECT img_desc FROM def_images WHERE id = '{$imdID}'";
    $result = mysql_query($query);
    $row = mysql_fetch_object($result);

    $pos = strpos($txt, "<flow:img ", $srcPosEnd);

    if ($row == false || strlen($row->img_desc) == 0) {
      continue;
    }
    $mathML = $row->img_desc;
    mysql_free_result($result);
    $imgURLs[$imgUrl] = $mathML;
  }
  $ret['html'] = $txt;
  $ret['imgURLs'] = $imgURLs;
  return $ret;
}

function save_definition($id, $lang, $def, $conn = false)
{
  if ($conn === true) {
    db_connect();
  }
  $query = "delete from definition where id='" . $id . "' and lang='{$lang}'";
  if (mysql_query($query) == false) {
    return false;
  }

  if ($lang < 7) {
    $def = utf8_decode($def);
  }
  $def = str_replace("'", "\'", $def);
  $def = str_replace('"', '\"', $def);
  $query = "insert into definition values ('" . $id . "','" . $lang . "','" . $def . "')";
  if (mysql_query($query) == false) {
    return false;
  }
  return true;
}
