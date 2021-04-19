<?php 
include_once("includes/socket/my_socket.php");
include_once("includes/file/file.php");
#include_once("includes/string/string.php");
include_once ('databaseinterface.php');

function getTDL($text, $transDirection) 
{ 
//  $text = utf8_decode($text);
   //file_put_contents("test.log",$text);

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
        $socket = my_socket_connect( "10.100.2.214", 4040 );

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
	writetofile("1.xml",$out_str);

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


	return $TransText;
} 

ini_set("soap.wsdl_cache_enabled", "0"); // отключаем кэширование WSDL
$server = new SoapServer("tdl.wsdl", array('encoding'=>'ISO-8859-1')); 
$server->addFunction("getTDL"); 
$server->handle(); 
?> 