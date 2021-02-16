<?php
	function log_error($error_str){
		if (isset($_SERVER['DOCUMENT_ROOT']) == TRUE)
		{
			$f_fime = false;
			$file_name = $_SERVER['DOCUMENT_ROOT']."/log/error.log";
			if(file_exists($file_name))
				$f_fime = fopen($file_name, 'a');
			else
				$f_fime = fopen($file_name, 'w');
			if($f_fime)
			{
				fwrite($f_fime,'['.date("c").']');
				if ($error_str == TRUE)
				{
					fwrite($f_fime,$error_str);
				}
				fwrite($f_fime,"\r\n");
					
				fclose($f_fime);
			}
		}
	}
?>