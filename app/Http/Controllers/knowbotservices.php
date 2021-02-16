<?php
include_once ('grammarinterface.php');
include_once ("databaseinterface.php");


class knowbotservices
{

    function convertAllData($userId)
    { 
  // file_put_contents("test.log","logged");
      return convert_all_data($userId);
    }
    function translateText($text, $transDirction)
    { 
      return translate_text($text, $transDirction);
    }
    function getTDL($text, $transDirction)
    { 
      return get_TDL($text, $transDirction);
    }
    function getHelp($lang, $helpType)
    { 
      return get_help($lang, $helpType);
    }
    function saveHelp($lang, $helpType, $helpText)
    { 
      return save_help($lang, $helpType, $helpText);
    }
    
    
    function searchByName($sp)
    {
      return search_by_name($sp);
    }
    function searchProgComment($word, $lang, $progType, $progStage, $progText, $commText)
    {
      return search_prog_comment($word, $lang, $progType, $progStage, $progText, $commText);
    }

    
    function getConcept($userId, $id) 
    {
      return get_concept($userId, $id);
    }
    function saveData($userId, $concept)
    {
      return save_data($userId, $concept);
    }
    function testConcept($concept)
    {
      return test_concept($concept);
    }
    function checkProg($progText)
    {
      return check_prog($progText);
    }
    function deleteConcept($userId, $id)
    {
      return delete_concept($userId, $id);
    }
    function createConcept($userId, $vol)
    {
      return create_concept($userId, $vol);
    }
    
    
    
    function getDeform($userId, $lang, $def_type, $section, $conditions)
    {
      return get_deform($userId, $lang, $def_type, $section, $conditions);
    }
    function createDeformTemplate($userId, $lang, $def_type, $section, $conditions, $new_section)
    {
      return get_deform($userId, $lang, $def_type, $section, $conditions, $new_section);
    }
    function saveDeform($userId, $lang, $def_type, $deforms, $changedDeforms) 
    {
      return save_deform($userId, $lang, $def_type, $deforms, $changedDeforms);
    }
    function deleteDeform($userId, $lang, $def_type, $section)
    {
      return delete_deform($userId, $lang, $def_type, $section);
    }
    
    
    
    function getDeformSubsection($userId, $lang, $def_type, $rel)
    {
      return get_deform_subsection($userId, $lang, $def_type, $rel);
    }
    function saveDeformSubsection($userId, $lang, $def_type, $rel, $vals)
    {
      return save_deform_subsection($userId, $lang, $def_type, $rel, $vals);
    }
    function deleteDeformSubsection($userId, $lang, $def_type, $rel)
    { 
      return delete_deform_subsection($userId, $lang, $def_type, $rel);
    }   
    
    
    
    function getSyntax($userId, $lang)
    {
      return get_syntax($userId, $lang);
    }
    function saveSyntax($userId, $lang, $synt)
    { 
      return save_syntax($userId, $lang, $synt);
    }
    
    
    
    function getMatrix($userId, $lang)
    {
      return get_matrix($userId, $lang);
    }
    function saveMatrix($userId, $lang, $mat)
    { 
      return save_matrix($userId, $lang, $mat);
    }
    function closeData($userId, $dataType, $p1, $p2)
    { 
      return close_data($userId, $dataType, $p1, $p2);
    }


    function getDefinition($id, $lang)
    {
      return get_definition($id, $lang);
    }
    function saveDefinition($id, $lang, $def)
    {
      return save_definition($id, $lang, $def, true);
    }
  

    function saveImageBase64($conceptId, $lang, $imageBase64, $mathml = "")
    { 
      return save_image_base64($conceptId, $lang, $imageBase64, $mathml);
    }

}

?>