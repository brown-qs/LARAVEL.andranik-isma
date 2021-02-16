<?php
include_once ('grammarinterface.php');
include_once ("databaseinterface.php");


class learningservices
{

    function getWord($word)
    { 
      db_connect();
      $word = utf8_decode($word);
      $word = str_replace("'", "\'", $word);
      $word = str_replace('"', '\"', $word);
      $query = "select w.id as id, w.word as word, d.def as def from words as w left join definition as d on d.id = w.id and d.lang = w.lang and w.lang = '1'  where word = '{$word}' and w.lang = '1'";
      $result = mysql_query($query);
      $ret = array();
      while ($row = mysql_fetch_object($result)) 
      {
        $tmp = new SearchResult();
        $tmp->id = $row->id;
        $tmp->roots = utf8_encode($row->def);       
        $tmp->word = utf8_encode($row->word);
        $ret[] = $tmp;
      }
      mysql_free_result($result);

      return $ret;
    }
    function getWordById($id)
    { 
      db_connect();
      $word = utf8_decode($word);
      $word = str_replace("'", "\'", $word);
      $word = str_replace('"', '\"', $word);
      $query = "select w.id as id, w.word as word, d.def as def from words as w left join definition as d on d.id = w.id and d.lang = w.lang and w.lang = '1'  where w.id = '{$id}' and w.lang = '1'";
      $result = mysql_query($query);
      $ret = array();
      while ($row = mysql_fetch_object($result)) 
      {
        $tmp = new SearchResult();
        $tmp->id = $row->id;
        $tmp->roots = utf8_encode($row->def);       
        $tmp->word = utf8_encode($row->word);
        $ret[] = $tmp;
      }
      mysql_free_result($result);

      return $ret;
    }
    
    function getRelations($id, $code1, $code2, $_code1, $_code2, $fl)
    {
      db_connect();
      $ret = array();
      
      
      if($_code1)
      {
          
          if($fl != 1)
          $query = "SELECT d.def as def, relation.id, relation.rel_num, relation.relation_type, relation.relation_subtype, relation.rel_id, relation.probability, words.word as rel_name  
                FROM relation, words left join definition as d on d.id = words.id and d.lang = words.lang
                WHERE relation.id = '".$id."'AND words.id=relation.rel_id AND words.synonym_num=1 AND words.lang=1 
                and ((relation.relation_type='{$code1}' and relation.relation_subtype='{$code2}') or (relation.relation_type='{$_code1}' and relation.relation_subtype='{$_code2}'))";
          else
          $query = "SELECT d.def as def, relation.id, relation.rel_num, relation.relation_type, relation.relation_subtype, relation.rel_id, relation.probability, words.word as rel_name  
                FROM relation, words left join definition as d on d.id = words.id and d.lang = words.lang
                WHERE relation.rel_id = '".$id."'AND words.id=relation.id AND words.synonym_num=1 AND words.lang=1 
                and ((relation.relation_type='{$code1}' and relation.relation_subtype='{$code2}') or (relation.relation_type='{$_code1}' and relation.relation_subtype='{$_code2}'))";
      }
      else
      {
          if($fl != 1)
          $query = "SELECT d.def as def, relation.id, relation.rel_num, relation.relation_type, relation.relation_subtype, relation.rel_id, relation.probability, words.word as rel_name  
                FROM relation, words left join definition as d on d.id = words.id and d.lang = words.lang
                WHERE relation.id = '".$id."'AND words.id=relation.rel_id AND words.synonym_num=1 AND words.lang=1 
                and relation.relation_type='{$code1}' and relation.relation_subtype='{$code2}'";
          else
          $query = "SELECT d.def as def, relation.id, relation.rel_num, relation.relation_type, relation.relation_subtype, relation.rel_id, relation.probability, words.word as rel_name  
                FROM relation, words left join definition as d on d.id = words.id and d.lang = words.lang
                WHERE relation.rel_id = '".$id."'AND words.id=relation.id AND words.synonym_num=1 AND words.lang=1 
                and relation.relation_type='{$code1}' and relation.relation_subtype='{$code2}'";
      }
      $result = mysql_query($query);
  
      $ret = array();
      while ($row = mysql_fetch_object($result)) 
      {
        $tmp = new SearchResult();
        $tmp->id = $row->id;
        $tmp->roots = utf8_encode($row->def);       
        $tmp->word = utf8_encode($row->rel_name);
        $ret[] = $tmp;
     }
     mysql_free_result($result);

     if($fl==2)
     {

       if($code1 > 128)
         $code1 -= 128;
       else
         $code1 += 128;

       if($_code1)
       { if($_code1 > 128)
           $_code1 += 128;
         else
           $_code1 -= 128;
       }
     
      if($_code1)
      {
          
          if($fl != 1)
          $query = "SELECT d.def as def, relation.id, relation.rel_num, relation.relation_type, relation.relation_subtype, relation.rel_id, relation.probability, words.word as rel_name  
                FROM relation, words left join definition as d on d.id = words.id and d.lang = words.lang
                WHERE relation.id = '".$id."'AND words.id=relation.rel_id AND words.synonym_num=1 AND words.lang=1 
                and ((relation.relation_type='{$code1}' and relation.relation_subtype='{$code2}') or (relation.relation_type='{$_code1}' and relation.relation_subtype='{$_code2}'))";
          else
          $query = "SELECT d.def as def, relation.id, relation.rel_num, relation.relation_type, relation.relation_subtype, relation.rel_id, relation.probability, words.word as rel_name  
                FROM relation, words left join definition as d on d.id = words.id and d.lang = words.lang
                WHERE relation.rel_id = '".$id."'AND words.id=relation.id AND words.synonym_num=1 AND words.lang=1 
                and ((relation.relation_type='{$code1}' and relation.relation_subtype='{$code2}') or (relation.relation_type='{$_code1}' and relation.relation_subtype='{$_code2}'))";
      }
      else
      {
          if($fl != 1)
          $query = "SELECT d.def as def, relation.id, relation.rel_num, relation.relation_type, relation.relation_subtype, relation.rel_id, relation.probability, words.word as rel_name  
                FROM relation, words left join definition as d on d.id = words.id and d.lang = words.lang
                WHERE relation.id = '".$id."'AND words.id=relation.rel_id AND words.synonym_num=1 AND words.lang=1 
                and relation.relation_type='{$code1}' and relation.relation_subtype='{$code2}'";
          else
          $query = "SELECT d.def as def, relation.id, relation.rel_num, relation.relation_type, relation.relation_subtype, relation.rel_id, relation.probability, words.word as rel_name  
                FROM relation, words left join definition as d on d.id = words.id and d.lang = words.lang
                WHERE relation.rel_id = '".$id."'AND words.id=relation.id AND words.synonym_num=1 AND words.lang=1 
                and relation.relation_type='{$code1}' and relation.relation_subtype='{$code2}'";
      }
      $result = mysql_query($query);
  
      $ret = array();
      while ($row = mysql_fetch_object($result)) 
      {
        $tmp = new SearchResult();
        $tmp->id = $row->id;
        $tmp->roots = utf8_encode($row->def);       
        $tmp->word = utf8_encode($row->rel_name);
        $ret[] = $tmp;
     }
     mysql_free_result($result);
     }

     return $ret;
   }

   function getFirstInstances($id)
   {
      db_connect();
      $ret = array();
      $query = "select w.id as id, w.word as word, d.def as def from class as c left join words as w on w.id = c.id and w.lang = '1' and w.synonym_num='1' left join definition as d on d.id = w.id and d.lang = w.lang where c.class_id='{$id}' and c.distance='1' group by w.id";

      $result = mysql_query($query);
  
      $ret = array();
      while ($row = mysql_fetch_object($result)) 
      {
        $tmp = new SearchResult();
        $tmp->id = $row->id;
        $tmp->roots = utf8_encode($row->def);       
        $tmp->word = utf8_encode($row->word);
        $ret[] = $tmp;
     }
     mysql_free_result($result);
     return $ret;
   }
}



?>
