<?php

define("ARM", 1);
define("ENG", 2);
define("WAM", 4);
define("RUS", 8);
define("OAM", 16);
define("LAT", 32);
define("TAL", 64);
define("CAPTION", 256);
define("REL", 512);
define("PROG", 1024);
define("DESC", 2048);
define("MASK_CLASS", 4096);
define("ENV", 8192);
define("DEFORM", 16384);
define("PROG_TEST",32768);
define("DELETE_CONCEPT",65536);
define("ADD_CONCEPT", 131072);
define("ADD_ELEMENTS", 262144);
define("UPDATE_ROOTS", 524288);
define("DEFORM_INDEXING", 1048576);
define("SYNTAX", 2097152);
define("MATRIX", 4194304);
define("DELETE_ROOTS", 8388608);
define("DEFINITION", 16777216);
define("TRK", 33554432);
define("GER", 67108864);
define("ITL", 134217728);
define("FRA", 268435456);
define("ZAZ", 536870912);
define("HAM", 1073741824);
define("ASR", 2147483648);
define("LEZ", 4294967296);
define("CHR", 8589934592);
define("LAZ", 17179869184);
define("PRS", 34359738368);
define("SPN", 68719476736);



class Word 
{
    public $id;
    public $syn;
    public $word;
    public $roots;
    public $r1;
    public $d1;
    public $r2;
    public $d2;
    public $r3;
    public $d3;
    public $r4;
    public $d4;
    public $r5;
    public $d5;
    public $mix;
    public $param;

    var $_explicitType = "ISMAKnowbotWord";
}

class Relation
{
    public $id;
    public $rel_num;
    public $code1;
    public $code2;
    public $conceptID;
    public $conceptName;
    public $prob;

    var $_explicitType = "ISMAKnowbotRelation";
}

class Program
{
    public $id;
    public $prog_num;
    public $lang;
    public $progType;
    public $stage;
    public $syn;
    public $progText;

    var $_explicitType = "ISMAKnowbotProgram";
}

class ConceptClass
{
    public $id;
    public $conceptName;
    public $prob;

    var $_explicitType = "ISMAKnowbotClass";
}

class ConceptCaption
{
    public $type;
    public $prob;

    var $_explicitType = "ISMAKnowbotConceptCaption";
}


class Concept
{
    public $caption;
    public $armWords;
    public $engWords;
    public $wamWords;
    public $rusWords;
    public $oamWords;
    public $latWords;
    public $talWords;
    public $trkWords;
    public $gerWords;
    public $itlWords;
    public $fraWords;
    public $zazWords;
    public $hamWords;
    public $asrWords;
    public $lezWords;
    public $chrWords;
    public $lazWords;
    public $prsWords;
    public $spnWords;
    public $relations;
    public $programs;
    public $classes;
    public $environments;
    public $desc;
    public $armDefinition;
    public $engDefinition;
    public $wamDefinition;
    public $rusDefinition;
    public $oamDefinition;
    public $latDefinition;
    public $talDefinition;
    public $trkDefinition;
    public $gerDefinition;
    public $itlDefinition;
    public $fraDefinition;
    public $zazDefinition;
    public $hamDefinition;
    public $asrDefinition;
    public $lezDefinition;
    public $chrDefinition;
    public $lazDefinition;
    public $prsDefinition;
    public $spnDefinition;
    
    var $_explicitType = "ISMAKnowbotConcept";
}

class SavingConcept extends Concept
{
    public $id;
    public $mask;

    var $_explicitType = "ISMAKnowbotSavingConcept";
}

class ReturnData
{
    public $error_code;
    public $error_string;

    var $_explicitType = "ISMAKnowbotReturnData";
}

class TranslateReturnData
{
    public $error_code;
    public $error_string;

    public $code;
    public $string;

    var $_explicitType = "ISMAKnowbotTranslateReturnData";
}

class ReturnData2
{
    public $error_code;
    public $user_name;
    public $data;
    var $_explicitType = "ISMAKnowbotReturnData2";
}

class SearchResult
{
    public $id;
    public $word;
    public $roots;
    public $syn;
    public $lang;

    var $_explicitType = "ISMAKnowbotSearchResult";

}

class SearchingProperties
{
    public $id;
    public $text;
    public $searchAsSubtext;
    public $searchInRoots;
    public $language;
    public $baseRole;
    public $baseRoleInv;
    public $frequency;
    public $frequencyInv;
    public $rootNumber;
    public $rootNumberInv;
    public $synNumber;
    public $synNumberInv;
    public $role;
    public $roleInv;
    public $defNumber;
    public $defNumberInv;
    public $relations;
    public $className;
    public $classNameInv;
    public $classInClasses;
    public $classDist;
    public $envName;
    public $envNameInv;
    public $envInEnvs;

    var $_explicitType = "ISMASearchingProperties";
}

class RelationSearch
{
    public $code1;
    public $code2;
    public $conceptName;
    public $prob;
    public $inv;
    public $andor;
    var $_explicitType = "ISMAKnowbotRelationSearch";
}

class Deform
{
  public $defItems;
  public $lang;
  public $id;
  public $defType;
  public $section;
  var $_explicitType = "ISMADeform";
}

class DeformItem
{
  public $id;
  public $p1;
  public $p2;
  public $p3;
  public $p4;
  public $p5;
  public $p6;
  public $p7;
  public $p8;
  public $p9;
  public $p10;
  public $p11;
  public $p12;
  public $p13;
  public $p14;
  public $p15;
  public $p16;
  public $p17;
  public $p18;
  public $p19;
  public $p20;
  public $p21;
  public $p22;
  public $p23;
  public $p24;
  public $p25;
  public $root;
  public $prep1;
  public $prep2;
  public $prep3;
  public $prep4;
  public $prep5;
  public $ending1;
  public $ending2;
  public $ending3;
  public $prefix;
  public $suffix;
  public $prep1_ind;
  public $prep2_ind;
  public $prep3_ind;
  public $prep4_ind;
  public $prep5_ind;
  public $ending1_ind;
  public $ending2_ind;
  public $ending3_ind;
  public $prefix_ind;
  public $suffix_ind;
  public $section;

  var $_explicitType = "ISMADeformItem";

} 


class DeformIndexing
{
  public $rel_subtype;
  public $values;
  var $_explicitType = "ISMADeformIndexing";

}

class Syntax
{
  public $id;
  public $rel;
  public $subRel;
  public $program;
  var $_explicitType = "ISMASyntax";
}

class Matrix
{
  public $id;
  public $code1;
  public $code2;
  public $code3;
  public $subCode1;
  public $subCode2;
  public $subCode3;
  public $prob;
  public $rule;
  var $_explicitType = "ISMAMatrix";
}
  

class User
{
  public $id;
  public $permission;
  public $rwPermission;
  public $userName;
  public $password;
  public $fName;
  public $lName;
  public $mName;
  public $email;
  public $phone;
  public $cPhone;
  public $address;
  var $_explicitType = "ISMAUser";
}

class UserTask
{
  public $id;
  public $currUserName;
  public $from_id;
  public $userName;
  public $taskDate;
  public $taskTime;
  public $task;
  var $_explicitType = "ISMAUserTask";
}

class UserTime
{
  public $id;
  public $duration;
  public $customDuration;
  public $date;
  public $coll;
  var $_explicitType = "ISMAUserTime";
}

class Test
{
  public $id;
  public $name;
  public $transDirection;
  var $_explicitType = "ISMAUserTest";
}

class TestContent
{
  public $test_id;
  public $num;
  public $src_text;
  public $dst_text;
  var $_explicitType = "ISMAUserTestContent";
}

class TestResult
{
  public $errorCount;
  public $testTime;
  public $errors;
  var $_explicitType = "ISMAUserTestResult";
}

global $data_base_files;

$data_base_files= array (
                   0 => "KnowledgeBase/EruditionBase/knowbot",
                   1 => "KnowledgeBase/DictBase/Armenian/Dictionary/dict",
                   2 => "KnowledgeBase/DictBase/English/Dictionary/dict",
                   3 => "KnowledgeBase/DictBase/WArmenian/Dictionary/dict",
                   4 => "KnowledgeBase/DictBase/Armenian/FastDict/fdict",
                   5 => "KnowledgeBase/DictBase/English/FastDict/fdict",
                   6 => "KnowledgeBase/DictBase/WArmenian/FastDict/fdict",
                   7 => "KnowledgeBase/DictBase/Armenian/Grammer/grammer",
                   8 => "KnowledgeBase/DictBase/English/Grammer/grammer",
                   9 => "KnowledgeBase/DictBase/WArmenian/Grammer/grammer",
                   10=> "KnowledgeBase/DictBase/Armenian/csint.arm",
                   11=> "KnowledgeBase/DictBase/English/csint.eng",
                   12=> "KnowledgeBase/DictBase/WArmenian/csint.wam",
                  );

