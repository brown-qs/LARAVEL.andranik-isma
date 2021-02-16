<?php

include_once ('databaseinterface.php');

define("MAX_SHORT", 65535);
define("PREPOSITION_REMOVAL", 16);
define("ENDING_REMOVAL", 23);
define("SIZEOF_DINT", 64);
define("ELEMENT_REMOVAL", 7);

function get_deform($userId, $lang, $def_type, $section, $conditions, $new_section = 0)
{
  db_connect();
  $query = "select id, section, p1, p2, p3, p4, p5, p6, p7, p8, p9, p10, p11, p12, p13, p14, p15, p16, p17, p18, p19, p20, 
                   p21, p22, p23, p24, p25, prep1, prep2, prep3, prep4, prep5, ending1, ending2, ending3, root, prefix, suffix, 
                   prep1_ind, prep2_ind, prep3_ind, prep4_ind, prep5_ind, ending1_ind, ending2_ind, ending3_ind, prefix_ind, suffix_ind
            from deform where lang = '".$lang."' and def_type='".$def_type."' and ";

  if($section != 0) $query = $query."section = '".$section."' and ";
  if($conditions['p1'] != -1 && $conditions['p1'] != 4294967295) $query = $query."p1 = '".$conditions['p1']."' and ";
  if($conditions['p2'] != -1 && $conditions['p2'] != 4294967295) $query = $query."p2 = '".$conditions['p2']."' and ";
  if($conditions['p3'] != -1 && $conditions['p3'] != 4294967295) $query = $query."p3 = '".$conditions['p3']."' and ";
  if($conditions['p4'] != -1 && $conditions['p4'] != 4294967295) $query = $query."p4 = '".$conditions['p4']."' and ";
  if($conditions['p5'] != -1 && $conditions['p5'] != 4294967295) $query = $query."p5 = '".$conditions['p5']."' and ";
  if($conditions['p6'] != -1 && $conditions['p6'] != 4294967295) $query = $query."p6 = '".$conditions['p6']."' and ";
  if($conditions['p7'] != -1 && $conditions['p7'] != 4294967295) $query = $query."p7 = '".$conditions['p7']."' and ";
  if($conditions['p8'] != -1 && $conditions['p8'] != 4294967295) $query = $query."p8 = '".$conditions['p8']."' and ";
  if($conditions['p9'] != -1 && $conditions['p9'] != 4294967295) $query = $query."p9 = '".$conditions['p9']."' and ";
  if($conditions['p10'] != -1 && $conditions['p10'] != 4294967295) $query = $query."p10 = '".$conditions['p10']."' and ";
  if($conditions['p11'] != -1 && $conditions['p11'] != 4294967295) $query = $query."p11 = '".$conditions['p11']."' and ";
  if($conditions['p12'] != -1 && $conditions['p12'] != 4294967295) $query = $query."p12 = '".$conditions['p12']."' and ";
  if($conditions['p13'] != -1 && $conditions['p13'] != 4294967295) $query = $query."p13 = '".$conditions['p13']."' and ";
  if($conditions['p14'] != -1 && $conditions['p14'] != 4294967295) $query = $query."p14 = '".$conditions['p14']."' and ";
  if($conditions['p15'] != -1 && $conditions['p15'] != 4294967295) $query = $query."p15 = '".$conditions['p15']."' and ";
  if($conditions['p16'] != -1 && $conditions['p16'] != 4294967295) $query = $query."p16 = '".$conditions['p16']."' and ";
  if($conditions['p17'] != -1 && $conditions['p17'] != 4294967295) $query = $query."p17 = '".$conditions['p17']."' and ";
  if($conditions['p18'] != -1 && $conditions['p18'] != 4294967295) $query = $query."p18 = '".$conditions['p18']."' and ";
  if($conditions['p19'] != -1 && $conditions['p19'] != 4294967295) $query = $query."p19 = '".$conditions['p19']."' and ";
  if($conditions['p20'] != -1 && $conditions['p20'] != 4294967295) $query = $query."p20 = '".$conditions['p20']."' and ";
  if($conditions['p21'] != -1 && $conditions['p21'] != 4294967295) $query = $query."p21 = '".$conditions['p21']."' and ";
  if($conditions['p22'] != -1 && $conditions['p22'] != 4294967295) $query = $query."p22 = '".$conditions['p22']."' and ";
  if($conditions['p23'] != -1 && $conditions['p23'] != 4294967295) $query = $query."p23 = '".$conditions['p23']."' and ";
  if($conditions['p24'] != -1 && $conditions['p24'] != 4294967295) $query = $query."p24 = '".$conditions['p24']."' and ";
  if($conditions['p25'] != -1 && $conditions['p25'] != 4294967295) $query = $query."p25 = '".$conditions['p25']."' and ";
  if($conditions['root'] != -1 && $conditions['root'] != 4294967295) $query = $query."root = '".$conditions['root']."' and ";
  if(strlen($conditions['prep1']) != 0) $query = $query."prep1 = '".utf8_decode($conditions['prep1'])."' and ";
  if(strlen($conditions['prep2']) != 0) $query = $query."prep2 = '".utf8_decode($conditions['prep2'])."' and ";
  if(strlen($conditions['prep3']) != 0) $query = $query."prep3 = '".utf8_decode($conditions['prep3'])."' and ";
  if(strlen($conditions['prep4']) != 0) $query = $query."prep4 = '".utf8_decode($conditions['prep4'])."' and ";
  if(strlen($conditions['prep5']) != 0) $query = $query."prep5 = '".utf8_decode($conditions['prep5'])."' and ";
  if(strlen($conditions['ending1']) != 0) $query = $query."ending1 = '".utf8_decode($conditions['ending1'])."' and ";
  if(strlen($conditions['ending2']) != 0) $query = $query."ending2 = '".utf8_decode($conditions['ending2'])."' and ";
  if(strlen($conditions['ending3']) != 0) $query = $query."ending3 = '".utf8_decode($conditions['ending3'])."' and ";
  if(strlen($conditions['prefix']) != 0) $query = $query."prefix = '".utf8_decode($conditions['prefix'])."' and ";
  if(strlen($conditions['suffix']) != 0) $query = $query."suffix = '".utf8_decode($conditions['suffix'])."' and ";

  $query = substr($query,0,strlen($query) - 4);
  $query = $query." group by section, id";

//  mysql_query($query);
  $result = mysql_query($query);
//  file_put_contents("test.log",$query);
  
  $ret = array();
  while ($row = mysql_fetch_object($result)) 
  {
    $tmp = new DeformItem();
    $tmp->prep1 = utf8_encode($row->prep1);
    $tmp->prep2 = utf8_encode($row->prep2);
    $tmp->prep3 = utf8_encode($row->prep3);
    $tmp->prep4 = utf8_encode($row->prep4);
    $tmp->prep5 = utf8_encode($row->prep5);
    $tmp->ending1 = utf8_encode($row->ending1);
    $tmp->ending2 = utf8_encode($row->ending2);
    $tmp->ending3 = utf8_encode($row->ending3);
    $tmp->prefix = utf8_encode($row->prefix);
    $tmp->suffix = utf8_encode($row->suffix);
    $tmp->root = $row->root;
    $tmp->id = $row->id;
    if($new_section == 0)
      $tmp->section = $row->section;
    else
      $tmp->section = $new_section;
    $tmp->p1 = $row->p1;
    $tmp->p2 = $row->p2;
    $tmp->p3 = $row->p3;
    $tmp->p4 = $row->p4;
    $tmp->p5 = $row->p5;
    $tmp->p6 = $row->p6;
    $tmp->p7 = $row->p7;
    $tmp->p8 = $row->p8;
    $tmp->p9 = $row->p9;
    $tmp->p10 = $row->p10;
    $tmp->p11 = $row->p11;
    $tmp->p12 = $row->p12;
    $tmp->p13 = $row->p13;
    $tmp->p14 = $row->p14;
    $tmp->p15 = $row->p15;
    $tmp->p16 = $row->p16;
    $tmp->p17 = $row->p17;
    $tmp->p18 = $row->p18;
    $tmp->p19 = $row->p19;
    $tmp->p20 = $row->p20;
    $tmp->p21 = $row->p21;
    $tmp->p22 = $row->p22;
    $tmp->p23 = $row->p23;
    $tmp->p24 = $row->p24;
    $tmp->p25 = $row->p25;

    $tmp->prep1_ind = $row->prep1_ind;
    $tmp->prep2_ind = $row->prep2_ind;
    $tmp->prep3_ind = $row->prep3_ind;
    $tmp->prep4_ind = $row->prep4_ind;
    $tmp->prep5_ind = $row->prep5_ind;
    $tmp->ending1_ind = $row->ending1_ind;
    $tmp->ending2_ind = $row->ending2_ind;
    $tmp->ending3_ind = $row->ending3_ind;
    $tmp->prefix_ind = $row->prefix_ind;
    $tmp->suffix_ind = $row->suffix_ind;

    $ret[] = $tmp;
  }
  
  mysql_free_result($result);

  $user_name = log_store($userId, 2, 3, $lang, $def_type, true);
  $ret2 = new ReturnData2();
  $ret2->error_code = 0;
  $ret2->user_name = "";
  $ret2->coll = $ret;
  if(strlen($user_name) != 0)
  {
    $ret2->error_code = 1;
    $ret2->user_name = $user_name;
  }
  
  return $ret2;
}



function prepEndingExisits($lang, $prep, $role, $parentType, $isPrep)
{


  $prep = utf8_decode($prep);
  if($prep == "0") {
    return 0;
  }
  $removal = PREPOSITION_REMOVAL;
  if(!$isPrep) $removal = ENDING_REMOVAL;

  $query = "select w.id as id
            from words as w left join caption as c on c.id = w.id 
            where lang = '".$lang."' and role1 = '".($role+$removal)."' and 
            c.parent_type = '".($parentType+$removal)."' and w.id < '".MAX_SHORT."' and w.word = '"."$".$prep."'";
            
            

  $result = mysql_query($query);
  if($result === false) {
      return false;
  }
  $id = mysql_fetch_object($result);
  if(!$id) return 0;
  mysql_free_result($result);

  $query = "select id from relation where rel_id={$id->id} and relation_type=168";

  $result = mysql_query($query);
  if($result === false) {
      return false;
  }
  $id = mysql_fetch_object($result);
  if(!$id) return 0;
  mysql_free_result($result);

  return $id->id;
}

function addPrepEnding($user_id, $lang, $prep, $role, $parentType, $isPrep, $shedule_id, &$ret_id)
{
  $ret_id = 0;
  if($prep == "0")
    return;

  $prep = utf8_decode($prep);
  $removal = PREPOSITION_REMOVAL;
  if(!$isPrep) $removal = ENDING_REMOVAL;

  $query = "select w.id 
            from words as w left join caption as c on c.id = w.id 
            where lang = '".$lang."' and role1 = '".$removal."' and 
            c.parent_type = '".$removal."' and w.id < '".MAX_SHORT."' and w.word = '".$prep."'";
            

  $result = mysql_query($query);
  if($result === false) {
      return false;
  }
  $id = mysql_fetch_object($result);
  $find = false;
  if(!$id)
  {
     $find = true;
     $new_id = create_concept($user_id, 0);

     $id = $new_id;
     $query = "insert into caption values ('".$new_id."','".$removal."','125')";
     if(mysql_query($query) === false) {
         return false;
     }

     $query = "insert into words values ('".$lang."','".$new_id."','1','".$prep."','".$prep."','".$prep."','".$removal."','0','0','0','0','0','0','0','0','0','0')";
      if(mysql_query($query) === false) {
          return false;
      }
     if($lang != 1)
     {
       $query = "insert into words values ('1','".$new_id."','1','@','@','@','0','0','0','0','0','0','0','0','0','0','0')";
         if(mysql_query($query) === false) {
             return false;
         }
     }
     if($lang != 2)
     {
       $query = "insert into words values ('2','".$new_id."','1','@','@','@','0','0','0','0','0','0','0','0','0','0','0')";
         if(mysql_query($query) === false) {
             return false;
         }
     }
     if($lang != 3)
     {
       $query = "insert into words values ('3','".$new_id."','1','@','@','@','0','0','0','0','0','0','0','0','0','0','0')";
         if(mysql_query($query) === false) {
             return false;
         }
     }
/*     if($lang != 4)
     {
       $query = "insert into words values ('4','".$new_id."','1','@','@','0','0','0','0','0','0','0','0','0','0','0')";
       mysql_query($query);
     }
     if($lang != 5)
     {
       $query = "insert into words values ('5','".$new_id."','1','@','@','0','0','0','0','0','0','0','0','0','0','0')";
       mysql_query($query);
     }*/
  }
  else $id = $id->id;
  $new_id = create_concept($user_id, 0);
  $query = "insert into caption values ('".$new_id."','".($parentType + $removal)."','125')";

    if(mysql_query($query) === false) {
        return false;
    }

  $query = "insert into words values ('".$lang."','".$new_id."','1','"."$".$prep."','"."$".$prep."','"."$".$prep."','".($role + $removal)."','0','0','0','0','0','0','0','0','0','0')";
  

file_put_contents("test.log",$query);  
    if(mysql_query($query) === false) {
        return false;
    }
  if($lang != 1)
  {
    $query = "insert into words values ('1','".$new_id."','1','@','@','@','0','0','0','0','0','0','0','0','0','0','0')";
      if(mysql_query($query) === false) {
          return false;
      }
  }
  if($lang != 2)
  {
    $query = "insert into words values ('2','".$new_id."','1','@','@','@','0','0','0','0','0','0','0','0','0','0','0')";
      if(mysql_query($query) === false) {
          return false;
      }
  }
  if($lang != 3)
  {
    $query = "insert into words values ('3','".$new_id."','1','@','@','@','0','0','0','0','0','0','0','0','0','0','0')";
      if(mysql_query($query) === false) {
          return false;
      }
  }
/*  if($lang != 4)
  {
    $query = "insert into words values ('4','".$new_id."','1','@','@','0','0','0','0','0','0','0','0','0','0','0')";
    mysql_query($query);
  }
  if($lang != 5)
  {
    $query = "insert into words values ('5','".$new_id."','1','@','@','0','0','0','0','0','0','0','0','0','0','0')";
    mysql_query($query);
  }*/
  $query = "insert into relation values ('".$id."','1','168','1','".$new_id."','100')";
    if(mysql_query($query) === false) {
        return false;
    }
  
  $ret_id = $id;
 
  $mask = 0;
  $mask = $mask|ARM|ENG|WAM|CAPTION;
  check($mask, $new_id, 0, 0, true);
//  $query = "insert into schedule (id, sub_id, mask, param, text_param) values ('".$shedule_id."','".$new_id."','".$mask."','0','')";
//  mysql_query($query);
//  $ret = send_converting_request($shedule_id);

  if($find == true)
  { 
    $mask = 0;
    $mask = $mask|ARM|ENG|WAM|CAPTION|REL|MASK_CLASS;
  //  $query = "insert into schedule values ('".$shedule_id."','".$id."','".$mask."','0','',now())";
    check($mask, $id, 0, 0, true);
//    mysql_query($query);
//    $ret = send_converting_request($shedule_id);
  }
    return true;
}


function get_changed_def_row($id, $deforms, &$def_ind)
{

  $def_row_count = count($deforms);

  for ($i = 0; $i < $def_row_count; $i++) 
  {
    if($deforms[$i]['id'] == $id)
    { $def_ind = $i;
      return $deforms[$i];
    }
  }
//  file_put_contents("test.log","0");

  return null;
}

/**
 * Convert binary to decimal
 * works with big numbers
 *
 * @param string $binary
 * @return float
 */


function unfucked_base_convert ($numstring, $frombase, $tobase) {

   $chars = "0123456789abcdefghijklmnopqrstuvwxyz";
   $tostring = substr($chars, 0, $tobase);

   $length = strlen($numstring);
   $result = '';
   for ($i = 0; $i < $length; $i++) {
       $number[$i] = strpos($chars, $numstring{$i});
   }
   do {
       $divide = 0;
       $newlen = 0;
       for ($i = 0; $i < $length; $i++) {
           $divide = $divide * $frombase + $number[$i];
           if ($divide >= $tobase) {
               $number[$newlen++] = (int)($divide / $tobase);
               $divide = $divide % $tobase;
           } elseif ($newlen > 0) {
               $number[$newlen++] = 0;
           }
       }
       $length = $newlen;
       $result = $tostring{$divide} . $result;
   }
   while ($newlen != 0);
   return $result;
}

function word128_and(&$h_word, &$l_word, $bit_pos)
{
  $h_word_bits = unfucked_base_convert($h_word, 10, 2);
  $l_word_bits = unfucked_base_convert($l_word, 10, 2);
  $len = strlen($h_word_bits);
  for($i = 0; $i < SIZEOF_DINT - $len; $i++)
    $h_word_bits = "0".$h_word_bits;

  $len = strlen($l_word_bits);
  for($i = 0; $i < SIZEOF_DINT - $len; $i++)
    $l_word_bits = "0".$l_word_bits;


  if($bit_pos < SIZEOF_DINT)
    $l_word_bits[SIZEOF_DINT - $bit_pos - 1] = "1";
  else
    $h_word_bits[SIZEOF_DINT - ($bit_pos - SIZEOF_DINT) - 1] = "1";
  $h_word = unfucked_base_convert($h_word_bits, 2, 10);
  $l_word = unfucked_base_convert($l_word_bits, 2, 10);

}

function word128_bit_check($h_word, $l_word, $bit_pos)
{
  $h_word_bits = unfucked_base_convert($h_word, 10, 2);
  $l_word_bits = unfucked_base_convert($l_word, 10, 2);
  $len = strlen($h_word_bits);
  for($i = 0; $i < SIZEOF_DINT - $len; $i++)
    $h_word_bits = "0".$h_word_bits;

  $len = strlen($l_word_bits);
  for($i = 0; $i < SIZEOF_DINT - $len; $i++)
    $l_word_bits = "0".$l_word_bits;

  if($bit_pos < SIZEOF_DINT)
  { if($l_word_bits[SIZEOF_DINT - $bit_pos - 1] == "1")
      return true;
    else
      return false;
  }
  else
  { 

   if($h_word_bits[SIZEOF_DINT - ($bit_pos - SIZEOF_DINT) - 1] == "1")
      return true;
    else
      return false;
  }
}

function add_element($lang, $parentType, $role, $param, $section, $name, &$ret_id)
{
  if($name == "0")
    $ret_id = 0;
  if($name == "-1")
  {  $ret_id = -1;
     return true;
  }
  if($name == "0" || $name == "-1")
    return 0;

  $name = utf8_decode($name);

  $query = "select id, h_decl, l_decl 
  	    from elements 
  	    where lang = '".$lang."' and parent_type='".$parentType."' and role='".$role."' and param='".$param."' and name='".$name."'";


  $result = mysql_query($query);
    if($result === false) {
        return false;
    }
  if(($row = mysql_fetch_object($result)))
  {
    $h_word = $row->h_decl;
    $l_word = $row->l_decl;
    $id = $row->id;

    if(!word128_bit_check($h_word, $l_word, $section - 1))
    {
      word128_and($h_word, $l_word, $section - 1);

      mysql_free_result($result);
      $query = "update elements set h_decl = '".$h_word."', l_decl = '".$l_word."' where id = '".$id."' and lang={$lang} and role={$role} and param={$param}";
        if(mysql_query($query) === false) {
            return false;
        }
      $ret_id = $id;                           
      return true;
    }
  }
  mysql_free_result($result);
  $h_word = 0;
  $l_word = 0;
  word128_and($h_word, $l_word, $section - 1);
  $query = "select max(id) as id from elements 
  	    where lang = '".$lang."' and parent_type='".$parentType."' and role='".$role."'";

  $result = mysql_query($query);
  $res = mysql_fetch_object($result);
  if(!$res) $new_id = 0;
  $new_id = $res->id + 1;
  mysql_free_result($result);

  $query = "insert into elements values ('".$lang."','".$new_id."','".$parentType."','".$role."','".$param."','".$h_word."','".$l_word."','".$name."')";
  $ret_id = $new_id;
    if(mysql_query($query) === false) {
        return false;
    }
  return true;
}
function save_deform($userId, $lang, $def_type, $deforms, $changedDeforms)
{
  $shedule_id = uniqid("");
  db_connect();
  mysql_query("START TRANSACTION");
  //mysql_query("lock tables deform write, elements write, words write, caption write");

  $role = $def_type;
  if($role == 11) $role = 1;
  $changed_def_row_count = count($changedDeforms);
  $elementAdded = false;
  $sections = array();
  for ($i = 0; $i < $changed_def_row_count; $i++) {
   $changedDeforms[$i]['suffix'] = str_replace("'", "\'", $changedDeforms[$i]['suffix']);
   $changedDeforms[$i]['prefix'] = str_replace("'", "\'", $changedDeforms[$i]['prefix']);
   $changedDeforms[$i]['prep1'] = str_replace("'", "\'", $changedDeforms[$i]['prep1']);
   $changedDeforms[$i]['prep2'] = str_replace("'", "\'", $changedDeforms[$i]['prep2']);
   $changedDeforms[$i]['prep3'] = str_replace("'", "\'", $changedDeforms[$i]['prep3']);
   $changedDeforms[$i]['prep4'] = str_replace("'", "\'", $changedDeforms[$i]['prep4']);
   $changedDeforms[$i]['prep5'] = str_replace("'", "\'", $changedDeforms[$i]['prep5']);
   $changedDeforms[$i]['ending1'] = str_replace("'", "\'", $changedDeforms[$i]['ending1']);
   $changedDeforms[$i]['ending2'] = str_replace("'", "\'", $changedDeforms[$i]['ending2']);
   $changedDeforms[$i]['ending3'] = str_replace("'", "\'", $changedDeforms[$i]['ending3']);
  }
  $def_row_count = count($deforms);
  for ($i = 0; $i < $def_row_count; $i++) 
  {
    $deforms[$i]['suffix'] = str_replace("'", "\'", $deforms[$i]['suffix']);
    $deforms[$i]['prefix'] = str_replace("'", "\'", $deforms[$i]['prefix']);
    $deforms[$i]['prep1'] = str_replace("'", "\'", $deforms[$i]['prep1']);
    $deforms[$i]['prep2'] = str_replace("'", "\'", $deforms[$i]['prep2']);
    $deforms[$i]['prep3'] = str_replace("'", "\'", $deforms[$i]['prep3']);
    $deforms[$i]['prep4'] = str_replace("'", "\'", $deforms[$i]['prep4']);
    $deforms[$i]['prep5'] = str_replace("'", "\'", $deforms[$i]['prep5']);
    $deforms[$i]['ending1'] = str_replace("'", "\'", $deforms[$i]['ending1']);
    $deforms[$i]['ending2'] = str_replace("'", "\'", $deforms[$i]['ending2']);
    $deforms[$i]['ending3'] = str_replace("'", "\'", $deforms[$i]['ending3']);
  }

  $ret1 = true;
  for ($i = 0; $i < $changed_def_row_count; $i++) 
  {

      if($changedDeforms[$i]['rowRemoved'])
      {
      
        $query = "delete from deform where id='".$changedDeforms[$i]['id']."' and lang='".$lang."' and def_type = '".$def_type."' and section = {$changedDeforms[$i]['section']}";
        //file_put_contents("test.log",$query);
        if(mysql_query($query) === false) {
            mysql_query("ROLLBACK");
            mysql_query("unlock tables");
            return false;
        }
        continue;
      
      }
      
      if($changedDeforms[$i]['changedPrep1'] || $changedDeforms[$i]['changedPrep2'] || $changedDeforms[$i]['changedPrep3'] ||
         $changedDeforms[$i]['changedPrep4'] || $changedDeforms[$i]['changedPrep5'] || $changedDeforms[$i]['changedEnding1'] ||
         $changedDeforms[$i]['changedEnding2'] || $changedDeforms[$i]['changedEnding3'])
      {
        $def_ind = 0;
        $row = get_changed_def_row($changedDeforms[$i]['id'], $deforms, $def_ind);
        $prep = "";
        $new_id = 0;
        $parentType = $row['p1'];
    
        
        if($changedDeforms[$i]['changedPrep1']!=0) 
        { $prep = $row['prep1'];
	      $id = prepEndingExisits($lang, $prep, $role, $row['p1'], true);
            if($id === false) {
                $ret1 = false;
            }
          if(!$id) 
          { if(addPrepEnding($userId, $lang, $prep, $role, $parentType, true, $shedule_id, $new_id) === false) {
              $ret1 = false;
            }
            $deforms[$def_ind]['prep1_ind'] = $new_id;
          }
	  else
 	    $deforms[$def_ind]['prep1_ind'] = $id;
        }
        if($changedDeforms[$i]['changedPrep2']!=0) 
        { $prep = $row['prep2'];
  	      $id = prepEndingExisits($lang, $prep, $role, $row['p1'], true);
            if($id === false) {
                $ret1 = false;
            }

          if(!$id) 
          { if(addPrepEnding($userId, $lang, $prep, $role, $parentType, true, $shedule_id, $new_id) === false) {
              $ret1 = false;
            }
            $deforms[$def_ind]['prep2_ind'] = $new_id;
          }
	      else
    	    $deforms[$def_ind]['prep2_ind'] = $id;
        }
        if($changedDeforms[$i]['changedPrep3']!=0) 
        { $prep = $row['prep3'];
          $id = prepEndingExisits($lang, $prep, $role, $row['p1'], true);
            if($id === false) {
                $ret1 = false;
            }

          if(!$id) 
          { if(addPrepEnding($userId, $lang, $prep, $role, $parentType, true, $shedule_id, $new_id) === false) {
              $ret1 = false;
            }
            $deforms[$def_ind]['prep3_ind'] = $new_id;
          }
	      else
    	    $deforms[$def_ind]['prep3_ind'] = $id;
        }
        if($changedDeforms[$i]['changedPrep4']!=0) 
        { $prep = $row['prep4'];
          $id = prepEndingExisits($lang, $prep, $role, $row['p1'], true);
            if($id === false) {
                $ret1 = false;
            }

          if(!$id) 
          { if(addPrepEnding($userId, $lang, $prep, $role, $parentType, true, $shedule_id, $new_id) === false) {
              $ret1 = false;
            }

            $deforms[$def_ind]['prep4_ind'] = $new_id;
          }
	      else
	        $deforms[$def_ind]['prep4_ind'] = $id;
        }
        if($changedDeforms[$i]['changedPrep5']!=0) 
        { $prep = $row['prep5'];
          $id = prepEndingExisits($lang, $prep, $role, $row['p1'], true);
            if($id === false) {
                $ret1 = false;
            }

          if(!$id) 
          { if(addPrepEnding($userId, $lang, $prep, $role, $parentType, true, $shedule_id, $new_id) === false) {
              $ret1 = false;
            }
            $deforms[$def_ind]['prep5_ind'] = $new_id;
          }
	      else
	        $deforms[$def_ind]['prep5_ind'] = $id;
        }

        if($changedDeforms[$i]['changedEnding1']!=0) 
        { $prep = $row['ending1'];
          $id = prepEndingExisits($lang, $prep, $role, $row['p1'], false);
            if($id === false) {
                $ret1 = false;
            }

          if(!$id) 
          {
            if(addPrepEnding($userId, $lang, $prep, $role, $parentType, false, $shedule_id, $new_id) === false) {
              $ret1 = false;
            }
            $deforms[$def_ind]['ending1_ind'] = $new_id;
          }
	      else
    	    $deforms[$def_ind]['ending1_ind'] = $id;
        }
        if($changedDeforms[$i]['changedEnding2']!=0) 
        { $prep = $row['ending2'];
          $id = prepEndingExisits($lang, $prep, $role, $row['p1'], false);
            if($id === false) {
                $ret1 = false;
            }

          if(!$id) 
          { if(addPrepEnding($userId, $lang, $prep, $role, $parentType, false, $shedule_id, $new_id) === false) {
              $ret1 = false;
            }
            $deforms[$def_ind]['ending2_ind'] = $new_id;
          }
	     else
	       $deforms[$def_ind]['ending2_ind'] = $id;
        }
        if($changedDeforms[$i]['changedEnding3']!=0) 
        { $prep = $row['ending3'];
	      $id = prepEndingExisits($lang, $prep, $role, $row['p1'], false);
            if($id === false) {
                $ret1 = false;
            }

          if(!$id) 
          { if(addPrepEnding($userId, $lang, $prep, $role, $parentType, false, $shedule_id, $new_id) === false) {
              $ret1 = false;
            }
            $deforms[$def_ind]['ending3_ind'] = $new_id;
          }
	      else
	       $deforms[$def_ind]['ending3_ind'] = $id;
        }
      }
      if($changedDeforms[$i]['prefixChanged']!=0)
      {
        $row = get_changed_def_row($changedDeforms[$i]['id'], $deforms, $def_ind);
        $ret = add_element($lang, $row['p1'], $role, $row['p2'], $row['section'], $row['prefix'], $new_id);
        if($ret === false) {
            $ret1 = false;
        }
        $deforms[$def_ind]['prefix_ind'] = $new_id;
        if($ret == true) $elementAdded = true;
      
      }
      
      if($changedDeforms[$i]['suffixChanged']!=0)
      {
        $row = get_changed_def_row($changedDeforms[$i]['id'], $deforms, $def_ind);
        $ret = add_element($lang, $row['p1'] + ELEMENT_REMOVAL, $role + ELEMENT_REMOVAL, $row['p2'], $row['section'], $row['suffix'], $new_id);
          if($ret === false) {
              $ret1 = false;
          }
        $deforms[$def_ind]['suffix_ind'] = $new_id;
        if($ret == true) $elementAdded = true;
      }

      if($changedDeforms[$i]['sectionChanged'])
      {
        $row = get_changed_def_row($changedDeforms[$i]['id'], $deforms, $def_ind);
        $ret = add_element($lang, $row['p1'], $role, $row['p2'], $row['section'], $row['prefix'], $new_id);
          if($ret === false) {
              $ret1 = false;
          }
        $deforms[$def_ind]['prefix_ind'] = $new_id;
        if($ret == true) $elementAdded = true;
        $ret = add_element($lang, $row['p1'] + ELEMENT_REMOVAL, $role + ELEMENT_REMOVAL, $row['p2'], $row['section'], $row['suffix'], $new_id);
          if($ret === false) {
              $ret1 = false;
          }
        $deforms[$def_ind]['suffix_ind'] = $new_id;
        if($ret == true) $elementAdded = true;
      }
      if($changedDeforms[$i]['p2Changed'])
      {
        $row = get_changed_def_row($changedDeforms[$i]['id'], $deforms, $def_ind);
        $ret = add_element($lang, $row['p1'], $role, $row['p2'], $row['section'], $row['prefix'], $new_id);
          if($ret === false) {
              $ret1 = false;
          }
        $deforms[$def_ind]['prefix_ind'] = $new_id;
        if($ret == true) $elementAdded = true;
        $ret = add_element($lang, $row['p1'] + ELEMENT_REMOVAL, $role + ELEMENT_REMOVAL, $row['p2'], $row['section'], $row['suffix'], $new_id);
          if($ret === false) {
              $ret1 = false;
          }
        $deforms[$def_ind]['suffix_ind'] = $new_id;
        if($ret == true) $elementAdded = true;
      }

      if($elementAdded)
      {
        $find_section = false;
        $count = count($sections);
        for ($j = 0; $j < $def_row_count; $j++) 
          if($sections[j] == $row['section'])
          {
            $find_section = true;
            break;
          }

        if(!$find_section)
        { 
          $query = "select id from deform where id <> '".$row['id']."' and lang = '".$lang."' and def_type='".$def_type."' and p2='".$row['p2']."' and section='".$row['section']."' and prefix='".$row['prefix']."' and suffix = '".$row['suffix']."'";
          
          $result = mysql_query($query);
          if($result === false) {
              mysql_query("ROLLBACK");
              mysql_query("unlock tables");
              return false;
          }

          $ret = mysql_fetch_object($result);
//		$ret = false;
          if(!$ret) 
          { 
            $sections[] = $row['section'];
          }
          mysql_free_result($result);
        }
      }
  }
  if($ret1 === false) {
      mysql_query("ROLLBACK");
      mysql_query("unlock tables");
      return false;
  }
  if($elementAdded)
  {
    $mask = 0;
    $mask = $mask|ADD_ELEMENTS;
//    $query = "insert into schedule values ('".$shedule_id."','0','".$mask."','".$lang."','',now())";
    check($mask, $lang);

//    mysql_query($query);
//    $ret = send_converting_request($shedule_id);
  }

  for ($i = 0; $i < count($sections); $i++) 
  {
     $mask = 0;  
     $mask = $mask|DELETE_ROOTS;
     check($mask, $lang, $def_type, $sections[$i]);
  }

  
  $def_row_count = count($deforms);
  $query = "select max(id) as max_id from deform where lang = '".$lang."' and def_type = '".$def_type."'";
  $result = mysql_query($query);
    if($result === false) {
        mysql_query("ROLLBACK");
        mysql_query("unlock tables");
        return false;
    }

  $ret = mysql_fetch_object($result);
  $max_id = $ret->max_id + 1;
  mysql_free_result($result);
  $find = false;
  $insert_query = "insert into deform values ";
  for ($i = 0; $i < $def_row_count; $i++) 
  {
    if($changedDeforms[$i]['rowRemoved'])
    { 
      //$find = true;
      continue;
    }

    $id = $deforms[$i]['id'];
    if($deforms[$i]['id'] < MAX_SHORT) // deform rows for update
    {
      $query = "delete from deform where id='".$deforms[$i]['id']."' and lang='".$lang."' and def_type = '".$def_type."' and section = {$deforms[$i]['section']}";
        if(mysql_query($query) === false) {
            mysql_query("ROLLBACK");
            mysql_query("unlock tables");
            return false;
        }

    }
    else
    {
      $id = $max_id;
      $max_id++;
    }
    $row = $deforms[$i];
    $insert_query = $insert_query."('".$lang."','".$def_type."','".$id."','".$row['section']."','".$row['p1']."','".
    $row['p2']."','".$row['p3']."','".$row['p4']."','".$row['p5']."','".$row['p6']."','".$row['p7']."','".$row['p8']."','".
    $row['p9']."','".$row['p10']."','".$row['p11']."','".$row['p12']."','".$row['p13']."','".$row['p14']."','".$row['p15']."','".
    $row['p16']."','".$row['p17']."','".$row['p18']."','".$row['p19']."','".$row['p20']."','".$row['p21']."','".$row['p22']."','".
    $row['p23']."','".$row['p24']."','".$row['p25']."','".utf8_decode($row['prep1'])."','".utf8_decode($row['prep2'])."','".utf8_decode($row['prep3'])."','".utf8_decode($row['prep4'])."','".
    utf8_decode($row['prep5'])."','".utf8_decode($row['prefix'])."','".$row['root']."','".utf8_decode($row['suffix'])."','".utf8_decode($row['ending1'])."','".utf8_decode($row['ending2'])."','".
    utf8_decode($row['ending3'])."','".$row['prep1_ind']."','".$row['prep2_ind']."','".$row['prep3_ind']."','".$row['prep4_ind']."','".
    $row['prep5_ind']."','".$row['prefix_ind']."','".$row['suffix_ind']."','".$row['ending1_ind']."','".$row['ending2_ind']."','".$row['ending3_ind']."'),";
    $find = true;
  }

  if($find)
  { $insert_query = substr($insert_query,0,strlen($insert_query) - 1);
      if(mysql_query($insert_query) === false) {
          mysql_query("ROLLBACK");
          mysql_query("unlock tables");
          return false;
      }

    $mask = 0;
    $mask = $mask|DEFORM;
    check($mask, $lang, $def_type);
 // $query = "insert into schedule values ('".$shedule_id."','".$def_type."','".$mask."','".$lang."','',now())";
   // mysql_query($query);

//    $ret = send_converting_request($shedule_id);
  
//    return $ret;
  }
  $find = false;
//  file_put_contents("test.log", count($sections)." -- ".$c);

  for ($i = 0; $i < count($sections); $i++) 
  {
     $mask = 0;  
     $mask = $mask|UPDATE_ROOTS;
     check($mask, $lang, $def_type, $sections[$i]);
  }

//  if($find)
  //  $ret = send_converting_request($shedule_id);
    //file_put_contents("test.log","commit");
    mysql_query("COMMIT");
    mysql_query("unlock tables");
  log_store($userId, 2, 4, $lang, $def_type);
}

function delete_deform($userId, $lang, $def_type, $section)
{
  db_connect();
  $delete_query = "delete from deform where lang = '".$lang."' and def_type = '".$def_type."' and section = '".$section."'";

  mysql_query($delete_query);

//  $shedule_id = uniqid("");
  $mask = 0;
  $mask = $mask|DEFORM;
//  $query = "insert into schedule values ('".$shedule_id."','".$def_type."','".$mask."','".$lang."','',now())";

  check($mask, $lang, $def_type);

  //mysql_query($query);

//  $ret = send_converting_request($shedule_id);
  
  log_store($userId, 2, 5, $lang, $def_type);

  return true;
}


function get_deform_subsection($userId, $lang, $def_type, $rel)
{
  db_connect();
  if($rel > 128) $rel = $rel - 128;

  $query = "select val,rel_subtype  from deform_indexing where 
            lang = '".$lang."' and def_type = '".$def_type."' and section = '".$rel."' order by rel_subtype, val";

  $result = mysql_query($query);

  $ret = array();
  $old_rel_subtype = 0;
  $str = "";
  $first_call = true;
  while ($row = mysql_fetch_object($result)) 
  {  
    if($first_call)
    {
      $first_call = false;
      $old_rel_subtype = $row->rel_subtype;
    }
    if($old_rel_subtype != $row->rel_subtype)
    {
      $tmp = new DeformIndexing();
      $tmp->rel_subtype = $old_rel_subtype;
      $str = substr($str,0,strlen($str) - 1);
      $tmp->values = $str;
      $old_rel_subtype = $row->rel_subtype;
      $str = ""; 
      $ret[] = $tmp;
    }
    $str = $str.$row->val.",";
  }
  if(strlen($str))
  { $tmp = new DeformIndexing();
    $tmp->rel_subtype = $old_rel_subtype;
    $str = substr($str,0,strlen($str) - 1);
    $tmp->values = $str;
    $old_rel_subtype = $row->rel_subtype;
    $str = ""; 
    $ret[] = $tmp;
  }

  mysql_free_result($result);
  $user_name = log_store($userId, 5, 3, $lang, $def_type, true);
  $ret2 = new ReturnData2();
  $ret2->error_code = 0;
  $ret2->user_name = "";
  $ret2->coll = $ret;
  if(strlen($user_name) != 0)
  {
    $ret2->error_code = 1;
    $ret2->user_name = $user_name;
  }
  return $ret2;
}

function save_deform_subsection($userId, $lang, $def_type, $rel, $vals)
{
  db_connect();
  mysql_query("START TRANSACTION");
  //mysql_query("lock tables deform_indexing write");
  if($rel > 128) $rel = $rel - 128;
  $delete_query = "delete from deform_indexing where lang = '".$lang."' and def_type = '".$def_type."' and section = '".$rel."'";
  if(mysql_query($delete_query) === false) {
      mysql_query("ROLLBACK");
      mysql_query("unlock tables");
      return false;
  }

  $insert_query = "insert into deform_indexing values ";
  $count = count($vals);
  for($i = 0; $i < $count; $i++)
  {
    $vek = explode(",", $vals[$i]["values"]);
    $item_count = count($vek);

    $str = "";
    for($j = 0; $j < $item_count; $j++)
    {
      $insert_query = $insert_query."('".$lang."','".$def_type."','".$rel."','".$vals[$i]['rel_subtype']."','".$vek[$j]."'),";
    }
  }
  $insert_query = substr($insert_query,0,strlen($insert_query) - 1);
//  file_put_contents("test.log",$insert_query);

  if(mysql_query($insert_query) === false) {
      mysql_query("ROLLBACK");
      mysql_query("unlock tables");
      return false;
  }

//  $shedule_id = uniqid("");
  $mask = 0;
  $mask = $mask|DEFORM_INDEXING;
//  $query = "insert into schedule values ('".$shedule_id."','".$def_type."','".$mask."','".$lang."','',now())";
  check($mask, $lang, $def_type);
//  mysql_query($query);

//  $ret = send_converting_request($shedule_id);
  mysql_query("COMMIT");
  mysql_query("unlock tables");
  log_store($userId, 5, 4, $lang, $def_type);

  return true;
}

function delete_deform_subsection($userId, $lang, $def_type, $rel)
{
  db_connect();
  if($rel > 128) $rel = $rel - 128;
  $delete_query = "delete from deform_indexing where lang = '".$lang."' and def_type = '".$def_type."' and section = '".$rel."'";
  mysql_query($delete_query);


//  $shedule_id = uniqid("");
  $mask = 0;
  $mask = $mask|DEFORM_INDEXING;
  check($mask, $lang, $def_type);
//  $query = "insert into schedule values ('".$shedule_id."','".$def_type."','".$mask."','".$lang."','',now())";
//  mysql_query($query);

//  $ret = send_converting_request($shedule_id);

  log_store($userId, 5, 5, $lang, $def_type);

  return true;

}

function get_syntax($userId, $lang)
{
  db_connect();
  $query = "select *  from syntax where lang = '".$lang."' order by id";
  $result = mysql_query($query);
  $ret = array();
  while ($row = mysql_fetch_object($result)) 
  {  
    $tmp = new Syntax();
    $tmp->id = $row->id;
    $tmp->rel = $row->main_type;
    $tmp->subRel = $row->sub_type;
    $tmp->program = utf8_encode(prog_ids_to_words($row->program));
    $ret[] = $tmp;
  }
  mysql_free_result($result);
  $user_name = log_store($userId, 3, 3, $lang, 0, true);
  $ret2 = new ReturnData2();
  $ret2->error_code = 0;
  $ret2->user_name = "";
  $ret2->coll = $ret;
  if(strlen($user_name) != 0)
  {
    //$ret2->error_code = 1;
    //$ret2->user_name = $user_name;
  }
  return $ret2;
}

function save_syntax($userId, $lang, $synt)
{
  set_time_limit(60);
  db_connect();
  mysql_query("START TRANSACTION");
  //mysql_query("lock tables syntax write");
  $ret = new ReturnData();

  $ret->error_code = 0;
  $ret->error_string ="";

  $count = count($synt);
  $foundNewSyntax = false;
  for($i = 0; $i < $count; $i++)
  {
    if($synt[$i]['id'] < 0)
      $foundNewSyntax = true;
    $prog_text = utf8_decode($synt[$i]['program']);
    $err_words = prog_words_to_id($prog_text);
    $synt[$i]['program'] = $prog_text;
    $err_caption = $synt[$i]['rel'].".".$synt[$i]['subRel']." Syntax Error: ";
    if(count($err_words))
    {
      $ret->error_string = $ret->error_string.$err_caption."The following words are not found in database - ";
      for ($i = 0; $i < count($err_words); $i++) 
        if($i==count($err_words)-1)
          $ret->error_string = $ret->error_string."'".$err_words[$i]."'\n";
        else
          $ret->error_string = $ret->error_string."'".$err_words[$i]."',";
      }
      else
      {
        $str = compile_prog($prog_text);
        $str = utf8_encode($str);
        if(strlen($str)!=0)
          $ret->error_string = $ret->error_string.$err_caption.$str;
      }

  }
  if(strlen($ret->error_string))
    return $ret;
  
  $delete_query = "delete from syntax where lang = '".$lang."'";
  if(mysql_query($delete_query) === false) {
      mysql_query("ROLLBACK");
      mysql_query("unlock tables");
      return false;
  }


  $insert_query = "insert into syntax values ";
  $count = count($synt);
  for($i = 0; $i < $count; $i++)
  {
    if($synt[$i]['id'] < 0) $synt[$i]['id'] = -$synt[$i]['id'];
//    $synt[$i]['program'] = utf8_decode($synt[$i]['program']);
    $synt[$i]['program'] = str_replace("'", "\'", $synt[$i]['program']);
    $synt[$i]['program'] = str_replace('"', '\"', $synt[$i]['program']);
    $insert_query = $insert_query."('".$lang."','".$synt[$i]['id']."','".$synt[$i]['rel']."','".$synt[$i]['subRel']."','".$synt[$i]['program']."'),";
  }

  $insert_query = substr($insert_query,0,strlen($insert_query) - 1);
  //file_put_contents("test.log",$insert_query);
  if(mysql_query($insert_query) === false) {
      mysql_query("ROLLBACK");
      mysql_query("unlock tables");
      return false;
  }


  $shedule_id = uniqid("");
  $mask = 0;
  $mask = $mask|SYNTAX;
  check($mask, $lang);
  
//  $ret = send_converting_request($shedule_id);

  if($foundNewSyntax)
  {
    $query = "update deform set p25 = 0 where deform.lang={$lang}";
    mysql_query($query);
    $query = "update deform join syntax on deform.lang=syntax.lang and syntax.lang = '".$lang."' and deform.p3=syntax.main_type+128 and deform.p4=syntax.sub_type set deform.p25=syntax.id";
    mysql_query($query);
    $query = "update deform join syntax on deform.lang=syntax.lang and syntax.lang = '".$lang."' and deform.p3=syntax.main_type+128 and deform.p25 = 0 and syntax.sub_type =0 set deform.p25=syntax.id";
      if(mysql_query($query) === false) {
          mysql_query("ROLLBACK");
          mysql_query("unlock tables");
          return false;
      }
          
//    $shedule_id = uniqid("");
    $mask = 0;
    $mask = $mask|DEFORM;
    check($mask, $lang, 0);
//    $query = "insert into schedule values ('".$shedule_id."','0','".$mask."','".$lang."','',now())";
  //  mysql_query($query);
//    $ret = send_converting_request($shedule_id);
  }
  mysql_query("COMMIT");
  mysql_query("unlock tables");
  log_store($userId, 3, 4, $lang, 0);
  return $ret;
}

function get_matrix($userId, $lang)
{
  db_connect();
  $query = "select * from matrix where lang = '".$lang."' order by id";
  $result = mysql_query($query);
  $ret = array();
  while ($row = mysql_fetch_object($result)) 
  {  
    $tmp = new Matrix();
    $tmp->id = $row->id;
    $tmp->code1 = $row->root_type;
    $tmp->code2 = $row->root_sub_type;
    $tmp->code3 = $row->root_prep_type;
    $tmp->subCode1 = $row->child_type;
    $tmp->subCode2 = $row->child_sub_type;
    $tmp->subCode3 = $row->child_prep_type;
    $tmp->prob = $row->prob;
    $tmp->rule = $row->rule;
    $ret[] = $tmp;
  }
  mysql_free_result($result);
  $user_name = log_store($userId, 4, 3, $lang, 0);
  $ret2 = new ReturnData2();
  $ret2->error_code = 0;
  $ret2->user_name = "";
  $ret2->coll = $ret;
  if(strlen($user_name) != 0)
  {
    $ret2->error_code = 1;
    $ret2->user_name = $user_name;
  }

  return $ret2;
}

function save_matrix($userId, $lang, $mat)
{ 
  db_connect();
  mysql_query("START TRANSACTION");
  //mysql_query("lock tables matrix write");
  $delete_query = "delete from matrix where lang = '".$lang."'";
  if(mysql_query($delete_query) === false) {
      mysql_query("ROLLBACK");
      mysql_query("unlock tables");
      return false;
  }

  $insert_query = "insert into matrix values ";
  $count = count($mat);
  for($i = 0; $i < $count; $i++)
    $insert_query = $insert_query."('".$lang."','".$mat[$i]['id']."','".$mat[$i]['code1']."','".$mat[$i]['code2']."','".$mat[$i]['code3'].
                    "','".$mat[$i]['subCode1']."','".$mat[$i]['subCode2']."','".$mat[$i]['subCode3']."','".$mat[$i]['prob']."','".$mat[$i]['rule']."'),";

  $insert_query = substr($insert_query,0,strlen($insert_query) - 1);
  //file_put_contents("test.log",$insert_query);
  if(mysql_query($insert_query) === false) {
      mysql_query("ROLLBACK");
      mysql_query("unlock tables");
      return false;
  }
  $mask = 0;
  $mask = $mask|MATRIX;
  check($mask, $lang);
//  $ret = send_converting_request($shedule_id);

  log_store($userId, 4, 4, $lang, 0);
  mysql_query("COMMIT");
  mysql_query("unlock tables");
  return true;
}


function copy_binary_database($source_root, $destination_root, $use_ssh = false)
{
   db_connect();
   global $data_base_files;
  
   if($use_ssh)
   {
     $connection = ssh2_connect('10.100.2.214', 22);
     if(!ssh2_auth_password($connection, 'root', 'sharik'))
     { 
       return;
     }
     $sftp = ssh2_sftp($connection);
   }


file_put_contents("test.log","count". count($data_base_files), FILE_APPEND);
   for($i = 0; $i < count($data_base_files); $i++)
   {
     $query = "select mod_time from db_files where file_name = '{$data_base_files[$i]}'";
     $result = mysql_query($query);
     $row = mysql_fetch_object($result);
     $dst_mtime = 0;
     if($row) $dst_mtime = $row->mod_time;
     mysql_free_result($result);

     $src_stat = stat($source_root.$data_base_files[$i]);
     
     if($dst_mtime != $src_stat['mtime'])
     {  
         
       $ret = false;

       file_put_contents("test.log",$source_root.$data_base_files[$i]." --- ".$destination_root.$data_base_files[$i], FILE_APPEND);
       if($use_ssh)
         $ret = ssh2_scp_send($connection, $source_root.$data_base_files[$i], $destination_root.$data_base_files[$i]);
       else
         $ret = copy($source_root.$data_base_files[$i], $destination_root.$data_base_files[$i]);

       if($ret)
       {

         if($row)
           $query = "update db_files set mod_time={$src_stat['mtime']} where file_name = '{$data_base_files[$i]}'";
         else
           $query = "insert into  db_files values('{$src_stat['mtime']}', '{$data_base_files[$i]}')";
         mysql_query($query);

       }
     }
   }
}


function convert_all_data($userId)
{
  set_time_limit(1800);
  
  $ret = new ReturnData();

  $user_name = log_store($userId, 5, 3, 0, 0);
  if(strlen($user_name)!=0) // if some user is now converting the base
  {
    $ret->error_code = 1;
    $ret->error_string = "The user '{$user_name}', is now updating the data base, please try translate in a few seconds";
    return $ret;
  }

  log_store($userId, 5, 4, 0, 0);

  
///	file_put_contents("test.log","send convert request");
//  $ret = send_converting_request(0);

//	file_put_contents("test.log","end convert request");

//  if($ret->error_code != 0)
//    return $ret;
 
//	file_put_contents("test.log","send translation request");
 send_translation_request("?", "?", "stop");
//	file_put_contents("test.log","end translation request");
  
//	file_put_contents("test.log","copy files");
  copy_binary_database(SOURCE_BINARY, DESTINAION_BINARY, false);
//	file_put_contents("test.log","end copy files");

//	file_put_contents("test.log","close data");
 close_data($userId, 5, 0, 0);
//	file_put_contents("test.log","return");
  
  return $ret;
}

function translate_text($text, $transDirction)
{
  $ret = new TranslateReturnData();
  $ret->code = 0;


  $user_name = log_store(0, 5, 3, 0, 0);
   ///file_put_contents("test.log",strlen($user_name));
  if(strlen($user_name) != 0) // if some user is now converting the base
  {
    $ret->error_code = 1;
    $ret->error_string = "The user '{$user_name}', is now updating the data base, please try translate in a few seconds";
    return $ret;
  }
  else
    close_data(0, 5, 0, 0);

  file_put_contents("messages.log", "");

  $dst_text = send_translation_request(utf8_decode($text), $transDirction, "translate");
  if(strlen($dst_text) == 0)
  {
    $ret->error_code = 1;
    $ret->error_string = "Some error occured during translation, tell about problem to administrator";
    return $ret;
  }
  $ret->error_code = 0;
  $ret->error_string = utf8_encode($dst_text);

  $messages = file_get_contents("messages.log");
  //file_put_contents("test.log", "text - ".$messages);
  if(strlen($messages) != 0)
  {
    $ret->code = 1;
    $ret->string = $messages;
  }

  return $ret;
}

function get_TDL($text, $transDirection) 
{ 
  $text = utf8_decode($text);

  $user_name = log_store(1000, 5, 3, 0, 0);
  if(strlen($user_name)) // if some user is now converting the base
    return "error";//"The user '{$user_name}', is now updatinf the data base, please try translate in a few seconds";
  else
    close_data(1000, 5, 0, 0);


	$PostText = trim($text);
	$PostText = cut_useful_text($PostText);
	$text_length = strlen($PostText);
	if($text_length == 0)
	{
		return "error";
	}

	$TransText = "";
	$trans_socket_timeout = 60;
        $socket = my_socket_connect( TRANSSERVER_HOST, TRANSSERVER_PORT );

	if($socket === false)
	{
		return "error";
	}

	$in_str = "action:translate;time-out:".$trans_socket_timeout.";content-type:tdl-text;language:{$transDirection};body-length:".strlen($PostText).";\r\n".$PostText;
	$in_str = GetIProto($in_str);

	if (my_socket_write_str($socket,$in_str) == false)
	{
		return "error";
	}

	$out_str = my_socket_read_nonblock2($socket, 1000000, $trans_socket_timeout /* + $add_time */, 5); 
	$out_str = substr($out_str,14);
//	writetofile("1.xml",$out_str);

	#echo "out2:".ArmAnsiToUTF8($out_str)."<br>";
	if ($out_str == false)
	{
		return "error";
	}

	$trans_header_len = strpos($out_str,"\n");
	#echo "len:".$trans_header_len."<br>";
	if ($trans_header_len == false)
	{
		return "error";
	}

	$trans_header = substr($out_str,0,$trans_header_len);
	if($trans_header == false)
	{
		return "error";
	}
	
	$TransText = substr($out_str,$trans_header_len+1);
	if($TransText == false)
	{
		return "error";
	}

  	file_put_contents("1.xml", $TransText);

	return utf8_encode($TransText);

} 



?>
