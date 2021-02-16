<?php
function SetInfo($file_name_prefix, $text)
{
	if ($file_name_prefix == false)
		$file_name_prefix = "unknow";
	
	if (isset($_SERVER['DOCUMENT_ROOT']) == TRUE)
	{
        $f_fime = false;
        if(file_exists($_SERVER['DOCUMENT_ROOT']."/Info/".$file_name_prefix."Times.txt"))
	        $f_fime = fopen($_SERVER['DOCUMENT_ROOT']."/Info/".$file_name_prefix."Times.txt", 'a');
        else
    	    $f_fime = fopen($_SERVER['DOCUMENT_ROOT']."/Info/".$file_name_prefix."Times.txt", 'w');
        if($f_fime)
        {
            fwrite($f_fime,date("c"));
			if ($text == TRUE)
			{
	        	fwrite($f_fime," -||- [Text: \"".$text."\"]");
			}
            fwrite($f_fime,"\r\n");
				
            fclose($f_fime);
        }

        $f_cnt = false;
        if(file_exists($_SERVER['DOCUMENT_ROOT']."/Info/".$file_name_prefix."Count.txt"))
	        $f_cnt = fopen($_SERVER['DOCUMENT_ROOT']."/Info/".$file_name_prefix."Count.txt", 'r+');
        else
    	    $f_cnt = fopen($_SERVER['DOCUMENT_ROOT']."/Info/".$file_name_prefix."Count.txt", 'w');

        if($f_cnt)
        {
			$cnt = 1;
         	$finfo = fstat($f_cnt);
			if (isset($finfo['size']))
            if ($finfo['size'] != 0)
            {
               	$cnt = fread($f_cnt,$finfo['size']);
                ++$cnt;
            }
                                
           ftruncate ($f_cnt,0);
           fseek($f_cnt,0,SEEK_SET);
           fwrite($f_cnt,$cnt);
           fclose($f_cnt);
        }
	}		
}
?>