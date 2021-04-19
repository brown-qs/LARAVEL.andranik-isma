<?php

function get_lang_name($lang)
{
  if($lang == 1) return "ARM";
  if($lang == 2) return "ENG";
  if($lang == 3) return "WAM"; 
  if($lang == 4) return "RUS"; 
  if($lang == 5) return "OAM"; 
  if($lang == 6) return "LAT"; 
  if($lang == 7) return "TAL"; 
  if($lang == 8) return "TRK"; 
  if($lang == 9) return "GER"; 
  if($lang == 10) return "ITL"; 
  if($lang == 11) return "FRA"; 
}

function get_concept_name($id)
{
  $query = "select word from words where lang='1' and id='".$id."' and synonym_num = 1";
  $result = mysql_query($query);
  $name = mysql_fetch_object($result);
  mysql_free_result($result);
  return ($name && $name->word) ? $name->word : '';
}

function get_concept_id($name)
{
  $name = str_replace("'", "\'", $name);
  $name = str_replace('"', '\"', $name);

  $query = "select id from words where lang='1' and word='".$name."' and synonym_num = 1";
  $result = mysql_query($query);
  $id = mysql_fetch_object($result);
  if(!$id) return 0;
  mysql_free_result($result);
  return $id->id;
}

function get_id_num($id)
{
  if(($id >> 16) == 1)
  {
    return $id&65535;
  }
  return 0;
}
function mix_id($vol, $num)
{
  
  $id = $vol;
  $id = intval($id) << 16;
  $id = $id | intval($num);
  return $id;
}

function str_id_to_num($id)
{
  $vol = substr($id, 0, strpos($id, "_"));
  $num = substr($id, strpos($id, "_") + 1, strlen($id) - strpos($id, "_") - 1);
  return mix_id($vol, $num);
}


function split_id($id, &$vol)
{
  $vol = $id >> 16;
  return $id&65535;
}     


/*
**********************************************************************************************
**********************************************************************************************
**********************************************************************************************
********************************* Role and Decl Checking *************************************
**********************************************************************************************
**********************************************************************************************
**********************************************************************************************
*/  

function check_decl_in_deform($role, $decl, $lang, $err_caption)
{
  db_connect();

  if($role > 7 || $role == 5)  
    return "";


  $query = "select count(*) as cnt from deform where lang = '".$lang."' and def_type='".$role."' and section='".$decl."'";

  $result = mysql_query($query);
  $count = mysql_fetch_object($result);
  mysql_free_result($result);

  //if($role == 1) $role = 1;

  if($count->cnt == 0)
    return $err_caption."There is no section '".$decl."' in deform".$role."\n";
  return "";
}

function check_parent_type_in_deform($type, $role, $decl, $lang, $err_caption)
{
  db_connect();

  if($role > 7 || $role == 5)  
    return "";

  //if($role == 1) $role = 11;
   $query = "select count(*) as cnt from deform 
             where lang = '".$lang."' and def_type='".$role."' and section='".$decl."' and p1='".$type."'";

  $result = mysql_query($query);
  $count = mysql_fetch_object($result);
  mysql_free_result($result);

//  if($role == 1) $role = 1;

  if($count->cnt == 0)
    return $err_caption."There is row with '".$type."' parent type in the section '".$decl."' of deform".$role."\n";
  return "";
}

function get_max_root_count($role, $decl, $lang)
{
  db_connect();
  if($role > 7 || $role == 5)  
    return "";

//  if($role == 1) $role = 11;
   $query = "select max(root) as cnt from deform where lang = '".$lang."' and def_type='".$role."' and section='".$decl."'";

  $result = mysql_query($query);
  $root_count = mysql_fetch_object($result);
  mysql_free_result($result);

  return $root_count->cnt;
}

function role_decl_def_check($role, $decl, $type, $root_count, $lang, $err_caption, $warr_caption)
{
  if($role > 7 || $role == 5)  
    return "";
  $ret = "";
  if($role!=0)
  { $str = check_decl_in_deform($role, $decl, $lang, $err_caption);
    if(strlen($str) == 0)
    { 
      $str = check_parent_type_in_deform($type, $role, $decl, $lang, $err_caption);
      if(strlen($str) == 0)
      { $ret = $ret.$str;
        $root_max_count = get_max_root_count($role, $decl, $lang);
        if($root_count < $root_max_count)
          $ret = $ret.$err_caption.
                 "The greatest root number in the section '".$decl."' of deform".
                  $role." is '".$root_max_count."'. It is greater then specified root count - '".$root_count."'\n";
      /*  if($root_count > $root_max_count)
          $ret = $ret.$warr_caption.
                 "The greatest root number in the section '".$decl."' of deform".
                  $role." is '".$root_max_count."'. It is less then specified root count - '".$root_count."'\n";*/
      }
      else $ret = $ret.$str;
    }
    else $ret = $ret.$str;
  }
  return $ret;
}

function role_decl_check($concept, $lang)
{
  $ret = "";
  $type = $concept['caption']['type'];

  if($lang >= 4 )//$lang == 4 || $lang == 5 || $lang == 6 || $lang == 7)
    return $ret;
  $langNam = get_lang_name($lang);
  $langWords =  strtolower($langNam)."Words";
    
  if($type == 0)
  {
    $query = "select parent_type as type from caption where id = '". $concept['id']."'";
    $result = mysql_query($query);
    $obj = mysql_fetch_object($result);
    mysql_free_result($result);
    $type = $obj->type;
  }

  if(empty($role)) $role = 0;
  if($role > 7 || $role == 5 || $type == 5)  
    return "";


  $syn_count = count($concept[$langWords]);
  for ($i = 0; $i < $syn_count; $i++) 
  {
    $syn = $concept[$langWords][$i];
    $syn["roots"] = utf8_decode($syn["roots"]);

    $roots = explode(",", $syn["roots"]);
    $root_count = count($roots);
    if($syn["roots"][0] == ',' || $syn["roots"][0] == '@') continue;

    
    $err_caption = "Error <".$langWords."(Synonym ".$syn["syn"]." ".$syn["roots"].")>: ";
    $warr_caption = "Warning <".$langWords."(Synonym ".$syn["syn"]." ".$syn["roots"].")>: ";

    if(($syn["r1"] && !$syn["d1"])||(!$syn["r1"] && $syn["d1"])||($syn["r2"] && !$syn["d2"])||(!$syn["r2"] && $syn["d2"]) ||
       ($syn["r3"] && !$syn["d3"])||(!$syn["r3"] && $syn["d3"])||($syn["r4"] && !$syn["d4"])||(!$syn["r4"] && $syn["d4"]))
     { $ret = $ret.$err_caption."Number missing in role - declination sequence'\n"; continue;}

    if($syn["r1"]<0||$syn["r1"]>28||$syn["r2"]<0||$syn["r2"]>28||$syn["r3"]<0||$syn["r3"]>28||$syn["r4"]<0||$syn["r4"]>28)
    { $ret = $ret.$err_caption."the role must be in the [0-28] interval'\n"; continue;}
    
    if($syn["d1"]<0||$syn["d1"]>128||$syn["d2"]<0||$syn["d2"]>128||$syn["d3"]<0||$syn["d3"]>128||$syn["d4"]<0||$syn["d4"]>128)
    { $ret = $ret.$err_caption."the declination must be in the [0-128] interval'\n"; continue;}

    
    if($syn["roots"][0] == '~')
    {
      if($lang == 2 && $syn["r1"] != 0)
      { $ret = $ret.$err_caption."The phrase can't has declination in English'\n"; continue;}

      if(($lang == 1||$lang == 3) && $root_count == 2 && $syn["r1"] != 0 && ($syn["r1"] != 13 ||$syn["d1"] != 1))
      { $ret = $ret.$err_caption."This phrase  with 2 roots can has '13.1' declination or has not any one\n"; continue;}
      else
        if(($lang == 1||$lang == 3) && $root_count != 2 && ($syn["r1"] != 0 ||$syn["d1"] != 0))
        { $ret = $ret.$err_caption."This phrase can't has declination\n"; continue;}
    }
    else
    {
      if($type >= 1 && $type <= 7 && $syn["r1"] != $type)
      { $ret = $ret.$err_caption."The first role must be equal to '".$type."'\n"; continue;}
      if($type >= 8 && $type <= 10 && ($syn["r1"] != $type || $syn["d1"] != $type))
      { $ret = $ret.$err_caption."The first role and decl must be equal to '".$type.".".$type."'\n"; continue;}
      if($type >= 16 && $type <= 28 && ($syn["d1"] != $type))
      { $ret = $ret.$err_caption."The first  decl must be equal to '1'\n"; continue;}
    }

    $ret = $ret.role_decl_def_check($syn["r1"], $syn["d1"], $type, $root_count, $lang, $err_caption, $warr_caption);
    $ret = $ret.role_decl_def_check($syn["r2"], $syn["d2"], $type, $root_count, $lang, $err_caption, $warr_caption);
    $ret = $ret.role_decl_def_check($syn["r3"], $syn["d3"], $type, $root_count, $lang, $err_caption, $warr_caption);
    $ret = $ret.role_decl_def_check($syn["r4"], $syn["d4"], $type, $root_count, $lang, $err_caption, $warr_caption);
  }
  return $ret;
} 

/*
**********************************************************************************************
**********************************************************************************************
**********************************************************************************************
*********************************** Relation Checking ****************************************
**********************************************************************************************
**********************************************************************************************
**********************************************************************************************
*/

function check_rel_in_deform($rel, $lang)
{
  $relCode1 = $rel['code1'];
  $relCode2 = $rel['code2'];

  db_connect();
  if($relCode1 != 168 && $relCode1 != 167 && $relCode1 != 39)
  {
    // Relations code1 and code2 checking in deformations
    if($relCode1  < 128) $relCode1 = $relCode1 + 128;
    if($relCode2 != 0)
      $query = "select count(*) as cnt
                from deform left join deform_indexing use index (ind1) on deform.lang='".$lang."' and deform_indexing.lang=deform.lang and 
                                      deform_indexing.def_type=deform.def_type and deform_indexing.rel_subtype=deform.p4 and deform_indexing.section=deform.p3-128 
                where p3='".$relCode1."' and (p4='".$relCode2."' or val='".$relCode2."') and deform.lang='".$lang."'";
    else
      $query = "select count(*) as cnt
                from deform 
                where p3='".$relCode1."' and deform.lang='".$lang."'";


    $result = mysql_query($query);
    $count = mysql_fetch_object($result);
    mysql_free_result($result);
    $langName = get_lang_name($lang);

    if($count->cnt == 0)
    {
      return "Error <Relations(".$relCode1."=".$relCode2.",".$rel['conceptName'].",".$rel['prob'].")>: Either Code1 or Code2 is not found in ".$langName." Deform\n";
    }
  }

  return "";
} 

/*
**********************************************************************************************
**********************************************************************************************
**********************************************************************************************
************************************ Roots Checking ******************************************
**********************************************************************************************
**********************************************************************************************
**********************************************************************************************
*/

function check_roots($concept, $lang)
{

  $ret = "";
  if($lang >= 4)// == 4 || $lang == 5 || $lang == 6 || $lang == 7)
    return $ret;
  $langNam = get_lang_name($lang);
  $langWords =  strtolower($langNam)."Words";
    
  $syn_count = count($concept[$langWords]);
  $found_symbol = false; // ¤ symbol
  $not_found_symbol = false; // ¤ symbol

  if($syn_count == 0)
  { $ret = "Error <".$langWords."> Concept must has at least one synonym\n";
    return $ret;
  }


  for ($i = 0; $i < $syn_count; $i++) 
  {
    $syn = $concept[$langWords][$i];
    $syn["roots"] = utf8_decode($syn["roots"]);
    $syn['word'] = utf8_decode($syn['word']);


    //file_put_contents("test.log",$syn["roots"]);
    
    $roots = explode(",", $syn["roots"]);
    $root_count = count($roots);


    if($syn["roots"][0] == ','|| $syn["roots"][0] == '@') continue;

    $err_caption = "Error <".$langWords."(Synonym ".$syn["syn"]." ".$syn["roots"].")>: ";
    $warr_caption = "Warning <".$langWords."(Synonym ".$syn["syn"]." ".$syn["roots"].")>: ";

    $tmp_found_symbol = false;

    for ($j= 0; $j < $root_count; $j++) 
    { $root_len = strlen($roots[$j]); // armati skzbic ev verjic chen karox linel prabelner

      if($roots[$j][0] == '¤')
        $tmp_found_symbol = true;

      if($root_len == 0 || ($root_len == 1 && ($roots[$j][0]=='*'||$roots[$j][0]=='#'||$roots[$j][0]=='~'||$roots[$j][0]=='¤')))
      { $ret = $ret.$err_caption."the root '".$roots[$j]."' is empty\n";
        continue;
      }

      if($roots[$j][0] == ' ' || $roots[$j][$root_len - 1] == ' ')
      { $ret = $ret.$err_caption."the root '".$roots[$j]."' cant has space in the beginning or on the end'\n"; 
        continue;
      }
      $fl = false;  
      for ($k= 0; $k < $root_len - 1; $k++) 
        if(($roots[$j][$k] == ' ' && $roots[$j][$k+1] == ' ')||($roots[$j][$k] == '*' && $roots[$j][$k+1] == '*')||
           ($roots[$j][$k] == '#' && $roots[$j][$k+1] == '#')||($roots[$j][$k] == '~' && $roots[$j][$k+1] == '~')|| 
           ($roots[$j][$k] == '¤' && $roots[$j][$k+1] == '¤')||($roots[$j][$k] == ',' && $roots[$j][$k+1] == ',')) 
        { $ret = $ret.$err_caption."the root '".$roots[$j]."' has more then one sequence of '".$roots[$j][$k]."' symbol'\n"; 
          $fl = true;
          break;
        }

      if($fl == true) continue;
      for ($k= 0; $k < $root_len - 1; $k++) 
        if(($roots[$j][$k] == '*' && $roots[$j][$k+1] == '#')||($roots[$j][$k] == '#' && $roots[$j][$k+1] == '*')||
           ($roots[$j][$k] == '~' && $roots[$j][$k+1] == '*')||($roots[$j][$k] == '~' && $roots[$j][$k+1] == '#')||
           ($roots[$j][$k] == '¤' && $roots[$j][$k+1] == '*')||($roots[$j][$k] == '¤' && $roots[$j][$k+1] == '#')||
           ($roots[$j][$k] == '¤' && $roots[$j][$k+1] == '~')||($roots[$j][$k] == '¤' && $roots[$j][$k+1] == ' ')||
           ($roots[$j][$k] == '~' && $roots[$j][$k+1] == ' ')||($roots[$j][$k] == '#' && $roots[$j][$k+1] == ' ')||
           ($roots[$j][$k] == '*' && $roots[$j][$k+1] == ' ')||($roots[$j][$k] == ' ' && $roots[$j][$k+1] == '*')||
           ($roots[$j][$k] == ' ' && $roots[$j][$k+1] == '#')||($roots[$j][$k] == ' ' && $roots[$j][$k+1] == '~')||
           ($roots[$j][$k] == ' ' && $roots[$j][$k+1] == '¤'))
        { $ret = $ret.$err_caption." the root '".$roots[$j]."' has wrong sequence of symbols '".$roots[$j][$k]."' and '".$roots[$j][$k+1]."'\n"; 
          $fl = true;
          break;
        }
      if($fl == true) continue;
      for ($k= 0; $k < $root_len; $k++) 
      {
        if($roots[$j][$k] == '*' && $k != $root_len - 1)
        { $ret = $ret.$err_caption."The symbol '*' must be the last symbol of the root '".$roots[$j]."'\n"; 
          $fl = true;
          break;
        }
        if($roots[$j][$k] == '#' && ($k != $root_len - 1 && $k != 0))
        { $ret = $ret.$err_caption."The symbol '#' must be either the first of the last symbol of the root '".$roots[$j]."'\n"; 
          $fl = true;
          break;
        }
        if($roots[$j][$k] == '~' && $k != 0)
        { $ret = $ret.$err_caption."The symbol '~' must be the first symbol of the root '".$roots[$j]."'\n"; 
          $fl = true;
          break;
        }
        if($roots[$j][$k] == '¤' && $k != $root_len - 1 && (!$k || ($k && ($roots[$j][$k-1]==')'||
           $roots[$j][$k-1]=='#' || $roots[$j][$k-1]=='~'))));
        else
        { 
          if($roots[$j][$k] == '¤')
          { $ret = $ret.$err_caption."Wrong place for symbol '¤' in the root '".$roots[$j]."'\n"; 
            $fl = true;
            break;
          }
        }
      }
      if($tmp_found_symbol)
        $found_symbol = true;
      else
        $not_found_symbol = true;
      if($fl == true) continue;
    }
  }

  if($found_symbol && $not_found_symbol)
    $ret = $ret.$err_caption."The symbol '¤' must have all roots'\n"; 

       //file_put_contents("test.log","first root check -- {$ret}      {$lang}");
  
  if(strlen($ret) == 0 && $lang == 1)
  { 
    for ($i = 0; $i < $syn_count; $i++) 
    {  
      $syn = $concept[$langWords][$i];
      if($syn["syn"] == 1) // the first synonym of armenian words
      {
        $syn['word'] = utf8_decode($syn['word']);
	if($syn['word'] == '@') {
	  continue;
	}
        $syn["roots"] = utf8_decode($syn["roots"]);

        $word = str_replace("'", "\'", $syn['word']);
        $word = str_replace('"', '\"', $word);
      
        $query = "select id from words where lang='1' and word='".$word."' and synonym_num = '1'";
        $result = mysql_query($query);
      
        while(($id = mysql_fetch_object($result)))
        {  
          if($id->id != $syn["id"])
          { $err_caption = "Error <".$langWords."(Synonym ".$syn["syn"]." ".$syn["roots"].")>: ";
            $ret = $ret.$err_caption."The word - '".$word."' exists in database\n";
            break;
          }
        }
        mysql_free_result($result);
      }
    }
  }
  return $ret;
}     
/*
**********************************************************************************************
**********************************************************************************************
**********************************************************************************************
************************************ Semantic Checking ***************************************
**********************************************************************************************
**********************************************************************************************
**********************************************************************************************
*/

function is_class_exists($classes, $num)
{
  $ret = false;
  $cls_count = count($classes);
  for ($i = 0; $i < $cls_count; $i++) 
  {
    $cls = $classes[$i];
    $cls_num = get_id_num($cls->id);
    if($cls_num == $num)
    { 
      $ret = true;
      break;
    }
  }
  return $ret;

}

function additional_semantic_check($concept)
{
  $ret = "";
  $cur_classes = get_class_ids($concept['conceptID']);
  $find11_1= false; 
  for ($i = 0; $i < $rel_count; $i++) 
  {
    $rel = $concept['relations'][$i];
    $code1 = $rel['code1'];
    $code2 = $rel['code2'];
    $rel['conceptName'] = utf8_decode($rel['conceptName']);

    $rel_name = $rel['conceptName'];
    
    $err_caption = "Error <Semantic(".code1."=".code2.",".$rel['conceptName'].",".$rel['prob'].")>: ";
    $classes = get_class_ids($rel['conceptID']);

    if($code1 == 12 && $code2 == 1 && !is_class_exists($classes, 76) && !is_class_exists($classes, 89))
    {
      $name1 = get_concept_name(76);
      $name2 = get_concept_name(89);
      $ret = $ret.$err_caption." concept - '".$rel_name."' must has in classes either '".$name1."' or '".$name2." concept\n";
    }
    if($code1 == 12 && $code2 == 2 && !is_class_exists($classes, 78) && !is_class_exists($classes, 89))
    {
      $name1 = get_concept_name(78);
      $name2 = get_concept_name(89);
      $ret = $ret.$err_caption." concept - '".$rel_name."' must has in classes either '".$name1."' or '".$name2." concept\n";
    }
    if($code1 == 11 && $code2 == 1 && is_class_exists($classes, 77) && !is_class_exists($cur_classes, 106))
    {
      $name2 = get_concept_name(106);
      $ret = $ret.$err_caption." Current concept must has in classes '".$name2."' concept\n";
    }
    if($code1 == 11 && $code2 == 2 && is_class_exists($classes, 89) && !is_class_exists($cur_classes, 104) && !is_class_exists($cur_classes, 165))
    {
      $name1 = get_concept_name(104);
      $name2 = get_concept_name(106);
      $ret = $ret.$err_caption." Current concept must has in classes either '".$name1."' or '".$name2." concept\n";
    }
    if($code1 == 11 && $code2 == 2 && is_class_exists($classes, 79) && !is_class_exists($cur_classes, 105))
    {
      $name2 = get_concept_name(105);
      $ret = $ret.$err_caption." Current concept must has in classes '".$name2."' concept\n";
    }
    if($code1 == 11 && $code2 == 2 && is_class_exists($classes, 78) && !is_class_exists($classes, 89) && !is_class_exists($cur_classes, 104))
    {
      $name2 = get_concept_name(104);
      $ret = $ret.$err_caption." Current concept must has in classes '".$name2."' concept\n";
    }
    if($code1 == 11 && $code2 == 1)
      $find11_1 = true;
  }

  if(!$find11_1 && is_class_exists($cur_classes, 106))
  { 
    $name = get_concept_name(106);
    $ret = $ret."Warning <Semantic>: Current concpet has in classes '".$name."' concept. Thus it must has 11.1 relation\n";
  }

  return $ret;
}


function semantic_check($concept)
{
  db_connect();
  $ret = "";
  
  
  $isVerb = true;
  if($concept['caption']['type'] != 2)
    $isVerb = false;
  
  
  $class_numbers = array();
  $rel_numbers = array();
  $rel_numbers[0][0] = 12; $rel_numbers[0][1] = 3;   $class_numbers[0][0] = 95; $class_numbers[0][1] = 0;
  $rel_numbers[1][0] = 12; $rel_numbers[1][1] = 4;   $class_numbers[1][0] = 97; $class_numbers[1][1] = 0;
  $rel_numbers[2][0] = 12; $rel_numbers[2][1] = 5;   $class_numbers[2][0] = 97; $class_numbers[2][1] = 0;
  $rel_numbers[3][0] = 12; $rel_numbers[3][1] = 0;   $class_numbers[3][0] = 96; $class_numbers[3][1] = 0;
  $rel_numbers[4][0] = 13; $rel_numbers[4][1] = 0;   $class_numbers[4][0] = 94; $class_numbers[4][1] = 0;
  $rel_numbers[5][0] = 14; $rel_numbers[5][1] = 0;   $class_numbers[5][0] = 93; $class_numbers[5][1] = 0;
  $rel_numbers[6][0] = 15; $rel_numbers[6][1] = 0;   $class_numbers[6][0] = 153; $class_numbers[6][1] = 0;
  $rel_numbers[7][0] = 16; $rel_numbers[7][1] = 0;   $class_numbers[7][0] = 154; $class_numbers[7][1] = 0;
  $rel_numbers[8][0] = 17; $rel_numbers[8][1] = 0;   $class_numbers[8][0] = 152; $class_numbers[8][1] = 0;
  $rel_numbers[9][0] = 18; $rel_numbers[9][1] = 0;   $class_numbers[9][0] = 96; $class_numbers[9][1] = 0;
  $rel_numbers[10][0] = 53; $rel_numbers[10][1] = 0; $class_numbers[10][0] = 239; $class_numbers[10][1] = 0;
  $rel_numbers[11][0] = 54; $rel_numbers[11][1] = 0; $class_numbers[11][0] = 240; $class_numbers[11][1] = 107;
  $rel_numbers[12][0] = 55; $rel_numbers[12][1] = 0; $class_numbers[12][0] = 241; $class_numbers[12][1] = 107;
  $rel_numbers[13][0] = 56; $rel_numbers[13][1] = 0; $class_numbers[13][0] = 243; $class_numbers[13][1] = 0;
  $rel_numbers[14][0] = 57; $rel_numbers[14][1] = 0; $class_numbers[14][0] = 233; $class_numbers[14][1] = 0;

  $main_classes = array();//get_class_ids($concept["id"]);

  $sstr = "";
  $rel_count = count($concept['relations']);
  $find_168 = false;
  $wrongType168 = false;
  $tmp = 0;
  for ($i = 0; $i < $rel_count; $i++) 
  {  
    $rel = $concept['relations'][$i];
    if($rel['code1'] == 168)
    {
      $classes = get_class_ids($rel['conceptID']);
      $tmp =  count($classes);
      for($j = 0; $j < $tmp; $j++)
        $main_classes[] = $classes[$j];
    }
  }




  for ($i = 0; $i < $rel_count; $i++) 
  {
    $rel = $concept['relations'][$i];
    if($rel['code1'] == 168)
      $find_168 = true;
    if($rel['code1'] == 168 && $rel['code2'] != 1 && $rel['code2'] != 2 && $rel['code2'] != 5 && $rel['code2'] != 15)
      $wrongType168 = true;
    if(!$isVerb) continue;
    $rel['conceptName'] = utf8_decode($rel['conceptName']);

    $err_caption = "Error <Semantic(".$rel['code1']."=".$rel['code2'].",".$rel['conceptName'].",".$rel['prob'].")>: ";
    $warr_caption = "Warning <Semantic(".$rel['code1']."=".$rel['code2'].",".$rel['conceptName'].",".$rel['prob'].")>: ";
    if($rel['code1'] == 168)
    { $classes = get_class_ids($rel['conceptID']);
      $class_length = count($classes);
      $fl = false;
      for ($j = 0; $j < $class_length; $j++) 
      {
        $cls = $classes[$j];
        $cls_num = get_id_num($cls->id);
        if($cls_num  == 0) continue;

        for ($k = 0; $k < count($class_numbers); $k++) 
          if($cls_num == $class_numbers[$k][0] || $cls_num == $class_numbers[$k][1])
          {
            $fl = true;
            break;
          }
        if($fl) break;
      }
      if(!$fl && $rel['prob'] > 100)
        $ret = $ret.$err_caption." the class-type relation probability must be in [0-100] interval\n";
      if($fl && $rel["prob"] < 101)
      {  $ret = $ret.$err_caption." the class-type relation probability must be in [101-199] interval\n";
//      file_put_contents("test.log",$cls_num );
      }
    }
    else
    {
      for ($k = 0; $k < count($rel_numbers); $k++) 
      { if(($rel_numbers[$k][1] == 0 && $rel['code1'] == $rel_numbers[$k][0]) || 
           ($rel_numbers[$k][1] != 0 && $rel['code1'] == $rel_numbers[$k][0] && $rel['code2'] == $rel_numbers[$k][1]))
        {
          if(is_class_exists($main_classes, $class_numbers[$k][0]) == false)
//          if(!$fl)
          {  
            if($class_numbers[$k][0] != 0 && is_class_exists($main_classes, $class_numbers[$k][1]) == false)
            { $cls_name = get_concept_name(mix_id(1,$class_numbers[$k][0]));
              if($rel['code1'] == 15)
                $ret = $ret.$warr_caption." concept with this relation must has in classes '".$cls_name."' concept\n";
              else
                $ret = $ret.$err_caption." concept with this relation must has in classes '".$cls_name."' concept\n";
            }
          }
          //break;
        }
      }
    }
  }

  $find_caption_class= false;
  $class_length = count($main_classes);
  for ($j = 0; $j < $class_length; $j++) 
  {
    $cls = $main_classes[$j];
    $cls_num = get_id_num($cls->id);
    if($concept["caption"]["type"] == $cls_num)
    {
      $find_caption_class = true;
    }
    if(!$isVerb) continue;

    for ($k = 0; $k < count($class_numbers); $k++) 
      if($cls_num == $class_numbers[$k][0] || ($cls_num == $class_numbers[$k][1] && $class_numbers[$k][1]))
      { $fl = false;
        for ($i = 0; $i < $rel_count; $i++) 
        {
          $rel = $concept['relations'][$i];
          if(($rel_numbers[$k][1] == 0 && $rel['code1'] == $rel_numbers[$k][0]) || 
             ($rel_numbers[$k][1] != 0 && $rel['code1'] == $rel_numbers[$k][0] && $rel['code2'] == $rel_numbers[$k][1]))
          { $fl = true;
            break;
          }
        }
        if(!$fl)
        {
          if($cls_num == $class_numbers[$k][0])
            $cls_name = get_concept_name(mix_id(1,$class_numbers[$k][0]));
          else
            $cls_name = get_concept_name(mix_id(1,$class_numbers[$k][1]));
          $ret = $ret."Warning <Semantic>: concept has in classes '".$cls_name."' concept. Thus it should has '".$rel_numbers[$k][0].".".$rel_numbers[$k][1]."' relation\n";
        }
        break;
      }
  }
  if(!$find_caption_class && $concept["caption"]["type"] >=1 && $concept["caption"]["type"] >=10)
  { 
    $cls_name = get_concept_name($concept["caption"]["type"]);
    $ret = $ret."Warning <Semantic>: concept with Parent Type '".$concept["caption"]["type"]."' must has in classes concept - '".$cls_name."'\n";
  }
  
  if($find_168 == false)
    $ret = $ret."Warning <Semantic>: Concept has not any 168 class \n";
  if($wrongType168 == true)
    $ret = $ret."Warning <Semantic>: Wrong subType of relation 168 \n";
  $ret = $ret.additional_semantic_check($concept);

  return $ret;
} 
/*
**********************************************************************************************
**********************************************************************************************
**********************************************************************************************
************************************* Program Checking ***************************************
**********************************************************************************************
**********************************************************************************************
**********************************************************************************************
*/
function prog_ids_to_words($program)
{
  $prog_len = strlen($program);
  for ($i = 0; $i < $prog_len; $i++) 
  {
    $start = -1;
    $end = -1;

    if($program[$i] == '_')
    { 
      for ($j = $i+1; $j < $prog_len; $j++) 
        if($program[$j] != '0' && $program[$j] != '1'&&$program[$j] != '2' && $program[$j] != '3'&&$program[$j] != '4' && $program[$j] != '5'&&$program[$j] != '6' && $program[$j] != '7'&&$program[$j] != '8' && $program[$j] != '9')
        {
          $end = $j-1;
          break;
        }

      for ($j = $i-1; $j >= 0; $j--) 
        if($program[$j] != '0' && $program[$j] != '1'&&$program[$j] != '2' && $program[$j] != '3'&&$program[$j] != '4' && $program[$j] != '5'&&$program[$j] != '6' && $program[$j] != '7'&&$program[$j] != '8' && $program[$j] != '9')
        {
          $start = $j+1;
          break;
        }
        
      if($start != -1 && $end != -1)
      {
        $id = substr($program, $start, $end - $start + 1);
        
        $name = get_concept_name(str_id_to_num($id));
        if($name)
        {
          $program = str_replace($id, "%".$name."%", $program);
          $prog_len = strlen($program);
          $i = 0;
        }
      }
    }
  }
  
  return $program;
}


function prog_words_to_id(&$program)
{

  $unfound_words = array();
  $prog_len = strlen($program);
  for ($i = 0; $i < $prog_len; $i++) 
  {
    $start = 0;
    $end = 0;

    if($program[$i] == '%')
    { $start = $i + 1;
      for ($j = $i+1; $j < $prog_len; $j++) 
        if($program[$j] == '%')
        {
          $end = $j-1;
          break;
        }
    }
    if($start && $end)
    {
      $word = substr($program, $start, $end - $start + 1);
      $id = get_concept_id($word);
      if($id == 0)
      {  $unfound_words[] = $word;
         $i = $end + 1;
      }
      else
      {
        $num = split_id($id, $vol);
        $pattern = "%".$word."%";
        $repl = $vol."_".$num;
        $program = str_replace($pattern, $repl, $program);

        $prog_len = strlen($program);
    //file_put_contents("test.log",$program." ---------------------- ".$pattern."   ".$repl);
        $i = 0;
      }
    }
  }
  return $unfound_words;
}

function compile_prog($program)
{
  db_connect();
  $ret = "";
  $shedule_id = uniqid("");
  $program = str_replace("'", "\'", $program);
  $program = str_replace('"', '\"', $program);

  $mask = 0;
  $mask = $mask|PROG_TEST;

  $query = "insert into schedule (id, sub_id,  mask, text_param) values ('".$shedule_id."','".$concept['id']."','".$mask."','{$program}')";

  $result = mysql_query($query);
    
  $err = send_converting_request($shedule_id);
  if($err->error_code == 2)
    $ret = $ret.$err->error_string."\n";
  return $ret;

} 

/*
**********************************************************************************************
**********************************************************************************************
**********************************************************************************************
*************************************** Test Concept *****************************************
**********************************************************************************************
**********************************************************************************************
**********************************************************************************************
*/


function test_concept($concept)
{
  $ret = new ReturnData();

  $ret->error_code = 0;
  $ret->error_string ="";
  
// return $ret;

  db_connect();


//********************************************** Caption Check ************************************************
  if($concept['mask']&CAPTION)
  {

    $type = $concept['caption']['type'];

    if($type < 16 || $type > 28) // if the type is not ending or preposition
    {
      if($type < 1 || $type > 14)
        $ret->error_string = $ret->error_string."Error <Caption>: Parent Type must be in the [1-14] interval\n";

      if($type >= 10 && $type <= 14)
        $ret->error_string = $ret->error_string."Warning <Caption>: Parent Type in the [10-14] interval is not freuently used\n";
    }

    if($concept['caption']['prob'] > 255 || $concept['caption']['prob'] < 0)
    {
      $ret->error_code = 1;
      $ret->error_string = $ret->error_string."Error <Caption>: Concept Probability must be in the [0-255] interval\n";
    }
  }

//******************************************** Relations Check ************************************************
  if($concept['mask']&REL)
  {

    $rel_count = count($concept['relations']);

    for ($i = 0; $i < $rel_count; $i++) 
    {
      $rel = $concept['relations'][$i];
      $relCode1 = $rel['code1'];
      $relCode2 = $rel['code2'];


      $rel['conceptName'] = utf8_decode($rel['conceptName']);

      $err_caption = "Error <Relations(".$relCode1."=".$relCode2.",".$rel['conceptName'].",".$rel['prob'].")>: ";

      if($rel['id'] == 0)
      {

        $word = str_replace("'", "\'", $rel['conceptName']);
        $word = str_replace('"', '\"', $word);

        $query = "select count(*) as cnt from words where lang='1' and synonym_num = 1 and word='".$word."'";
        $result = mysql_query($query);
        $count = mysql_fetch_object($result);
        mysql_free_result($result);
        if($count->cnt == 0)
          $ret->error_string = $ret->error_string.$err_caption."relation concept name - '".$rel['conceptName']."' doesn't exist in the database\n";
      }

      // Relations code1 and code2 checking 
    
      if($relCode1 < 11 || $relCode1 > 250)
        $ret->error_string = $ret->error_string.$err_caption."code1 value must be in [11-250] interval\n";
      else
      { if($relCode2 < 0 || $relCode2 > 250)
          $ret->error_string = $ret->error_string.$err_caption."code2 value must be in [0-250] interval\n";
        else
        {
          if($relCode1 != 168 && $relCode1 != 167 && $relCode1 != 39 && $relCode1 != 79)
          {
            if(($relCode1 == 29 && $relCode2 == 79)||($relCode1 == 32 && $relCode2 == 79)||($relCode1 == 33 && $relCode2 == 79));
            else
            { $ret->error_string = $ret->error_string.check_rel_in_deform($rel, 1);
              $ret->error_string = $ret->error_string.check_rel_in_deform($rel, 2);
              $ret->error_string = $ret->error_string.check_rel_in_deform($rel, 3);
            }
          }
        }
      }  
    
      // Relation Probability checking
      if($relCode1 != 168 && ($rel['prob'] < 0 || $rel['prob'] > 100) )
        $ret->error_string = $ret->error_string.$err_caption."relation probability value must be in [0-100] interval\n";
      else
        if($relCode1 == 168 && ($rel['prob'] < 0 || $rel['prob'] > 200) )
        $ret->error_string = $ret->error_string.$err_caption."class relelation probability value must be in [0-200] interval\n";
    }
    $ret->error_string = $ret->error_string.semantic_check($concept);
  }
  if($concept['mask']&ARM) 
  {
    $ret->error_string = $ret->error_string.check_roots($concept, 1);
    $ret->error_string = $ret->error_string.role_decl_check($concept, 1);
  }
  if($concept['mask']&ENG) 
  {
    $ret->error_string = $ret->error_string.check_roots($concept, 2);
    $ret->error_string = $ret->error_string.role_decl_check($concept, 2);
  }
  if($concept['mask']&WAM) 
  {
    $ret->error_string = $ret->error_string.check_roots($concept, 3);
    $ret->error_string = $ret->error_string.role_decl_check($concept, 3);
  }
  if($concept['mask']&RUS) 
  {
    $ret->error_string = $ret->error_string.check_roots($concept, 4);
    $ret->error_string = $ret->error_string.role_decl_check($concept, 4);
  }
  if($concept['mask']&OAM) 
  {
    $ret->error_string = $ret->error_string.check_roots($concept, 5);
    $ret->error_string = $ret->error_string.role_decl_check($concept, 5);
  }
  if($concept['mask']&LAT) 
  {
    $ret->error_string = $ret->error_string.check_roots($concept, 6);
    $ret->error_string = $ret->error_string.role_decl_check($concept, 6);
  }
  if($concept['mask']&TAL) 
  {
    $ret->error_string = $ret->error_string.check_roots($concept, 7);
    $ret->error_string = $ret->error_string.role_decl_check($concept, 7);
  }
  if($concept['mask']&TRK)
  {
  	$ret->error_string = $ret->error_string.check_roots($concept, 8);
  	$ret->error_string = $ret->error_string.role_decl_check($concept, 8);
  }
  if($concept['mask']&GER)
  {
  	$ret->error_string = $ret->error_string.check_roots($concept, 9);
  	$ret->error_string = $ret->error_string.role_decl_check($concept, 9);
  }
  if($concept['mask']&ITL)
  {
  	$ret->error_string = $ret->error_string.check_roots($concept, 10);
  	$ret->error_string = $ret->error_string.role_decl_check($concept, 10);
  }
  if($concept['mask']&FRA)
  {
  	$ret->error_string = $ret->error_string.check_roots($concept, 11);
  	$ret->error_string = $ret->error_string.role_decl_check($concept, 11);
  }
  

  if($concept['mask']&PROG)
  {

    $prog_count = count($concept['programs']);

    for ($i = 0; $i < $prog_count; $i++) 
    {
      $pr = $concept['programs'][$i];
      $lang_name = get_lang_name($pr['lang']);
      if($pr['progType'] == 1) $prog_type = "ANALYS";
      if($pr['progType'] == 2) $prog_type = "SYNTHESES";
      if($pr['progType'] == 3) $prog_type = "TREE";
      
      $err_caption = "Error <Program(".$lang_name." ".$prog_type." STAGE ".$pr['stage']." SYN ".$pr['syn'].")>: ";
    

      if($pr['progType'] == 1 && ($pr['stage'] < 0 || $pr['stage'] > 7))
      { $ret->error_string = $ret->error_string.$err_caption."Analyse program stage must be in [0-7] interval";
        continue;
      }

      if($pr['progType'] == 2 && ($pr['stage'] < 0 || $pr['stage'] > 4))
      { $ret->error_string = $ret->error_string.$err_caption."Synthesis program stage must be in [0-4] interval";
        continue;
      }

      if($pr['progType'] == 3 && $pr['stage'] != 8)
      { $ret->error_string = $ret->error_string.$err_caption."Tree program stage must be equal to 8";
        continue;
      }

      $langNam = get_lang_name($pr['lang']);
      $langWords =  strtolower($langNam)."Words";
      $syn_count = count($concept[$langWords]);
      if($syn_count == 0)
      {
        $query = "select count(*) as cnt from words where lang='{$pr['lang']}' and id='{$concept['id']}'";
  //file_put_contents("test.log",$query);


        $result = mysql_query($query);
        $id = mysql_fetch_object($result);
        if(!$id) $syn_count = 0;
        else $syn_count = $id->cnt;
      
      }
      if($pr['syn'] > $syn_count)
      {
        $ret->error_string = $ret->error_string.$err_caption." There is no synonym with number ".$pr['syn']."\n";
        continue;
      }
      if($pr['syn'] < 0)
      {
        $ret->error_string = $ret->error_string.$err_caption." Synonym number cant be negative\n";
        continue;
      }


      $prog_text = utf8_decode($pr['progText']);

      $err_words = prog_words_to_id($prog_text);
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
        if(strlen($str)!=0)
          $ret->error_string = $ret->error_string.$err_caption.$str;
      }
    }
  }
  $err_pos = strpos($ret->error_string, "Error");
  $warr_pos = strpos($ret->error_string, "Warning");

  if($err_pos === false)
  { if($warr_pos === FALSE);
    else
      $ret->error_code = 2;
  }
  else
    $ret->error_code = 1;
  $ret->error_string = utf8_encode($ret->error_string);

  return $ret;
} 


function check_prog($progText)
{
  db_connect();

  $ret = new ReturnData();

  $ret->error_code = 0;
  $ret->error_string ="";

  $prog_text = utf8_decode($progText);
  $err_words = prog_words_to_id($prog_text);
  if(count($err_words))
  {
    $ret->error_code = 1;
    $ret->error_string = $ret->error_string."The following words are not found in database - ";
    for ($i = 0; $i < count($err_words); $i++) 
      if($i==count($err_words)-1)
        $ret->error_string = $ret->error_string."'".$err_words[$i]."'\n";
       else
        $ret->error_string = $ret->error_string."'".$err_words[$i]."',";
  }
  else
  {
    $str = compile_prog($prog_text);
    if(strlen($str)!=0)
    {  $ret->error_string = $ret->error_string.$str;
       $ret->error_code = 1;
    }
  }
  $ret->error_string = utf8_encode($ret->error_string);
  return $ret;

}