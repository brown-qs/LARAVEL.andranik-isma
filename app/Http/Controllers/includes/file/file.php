<?php
############### READ #############################################
function readfromfile($filename)
{
	if (is_readable($filename))
		return file_get_contents($filename);
	else
		return FALSE;

}               
############### write ############################################
function writetofile($filename,$str)
{
	return file_put_contents($filename,$str);
}               

function appendtofile($filename,$str)
{
	return file_put_contents($filename,$str,FILE_APPEND);
/*	
	if ($filename == false)
		return 0;

        $handle = fopen($filename, 'a');
        $wb = 0;
        if($handle)
        {
		$wb = fwrite($handle, $str);
		fclose($handle);
        }

        return $wb;
*/
}               

function setslash($dirname)
{
	if ($dirname == FALSE)
		return FALSE;
	
	if ($dirname[strlen($dirname)-1] != '\\' && $dirname[strlen($dirname)-1] != '/' )
	{
		if (strpos($dirname,"/"))
			$dirname .= "/";
		else
			$dirname .= "\\";
	}
	
	return $dirname;
}
############### delete ###########################################
#Here is simple function that will find and remove all files (except "." ones) that match the expression ($match, "*" as wildcard) under starting directory ($path) and all other directories under it.

function deletefiles_($root_dir,$patern){
	if ($root_dir == FALSE || $patern == FALSE)
		return FALSE;

	$root_dir = setslash($root_dir);
	$deld = 0;
	$dirs = glob($root_dir."*",GLOB_MARK | GLOB_ONLYDIR);
#	echo nl2br(print_r($dirs,true));
	$files = glob($root_dir.$patern);
#	echo nl2br(print_r($files,true));
	foreach($files as $file){
		if(is_file($file)){
			unlink($file);
			$deld++;
		}
	}

	foreach($dirs as $subdir){
		if(is_dir($subdir)){
			$deld +=deletefiles_($subdir,$patern);
		}
	}
   return $deld;
} 

function deletefiles($patern){
   static $deld = 0;
   $files = glob($patern);
#  echo nl2br(print_r($files,true));
   foreach($files as $file){
      if(is_file($file)){
         unlink($file);
         $deld++;
      }
   }
   return $deld;
} 

function deletefile($file){
      if(is_file($file) == FALSE)
	  {
	  	return FALSE;
	  }
	  
	  return  unlink($file);
} 

############### read write file line##############################
function fl_writeifnotexists($filename,$str)
{
	$fl_write_str = "\n".$str;
	$infilestr = readfromfile($filename);
	if ($infilestr == false)
		return writetofile($filename,$fl_write_str);
	
	
	if (fl_isexists($infilestr,$str) == FALSE)
		return appendtofile($filename,$fl_write_str);

	return 0;
}

function fl_readtoarray($filename)
{

	$infilestr = readfromfile($filename);
	if ($infilestr == false)
		return 0;

		
	$it_array = array();
	$fl_count = 0;
	
	$tok = "";
	$tok = strtok($infilestr, "\n\r");
	while ($tok !== false) 
	{
		++$fl_count;
		$it_array = array_pad($it_array,$fl_count,$tok);
		$tok = strtok("\n\r");
	}

	return $it_array;
}

function fl_isexists($fl_instr,$if_findstr)
{
	$fl_instr = strtolower($fl_instr);
	$if_findstr = strtolower($if_findstr);

	#$tok = "";
	$tok = strtok($fl_instr, "\n\r");
	while ($tok !== false) 
	{
		if ($if_findstr == $tok)
			return TRUE;
		$tok = strtok("\n\r");
	}

	return FALSE;
}


function saveimages(&$imgarray,$nameprefix, $imgdir)
{
	$imgdir = setslash($imgdir);
	if (!file_exists($imgdir))
	if (!mkdir($imgdir))
	{
#		echo "cant create dir:{$imgdir}<br>";
		return FALSE;
	}
	
	deletefiles($imgdir.$nameprefix."*");
	
	$imgfilenames = "";
	$filecount = count($imgarray['name']);
	$savecount = 0;
	for ($index = 0; $index < $filecount; ++$index){
#		echo "see tmp:{$imgarray['tmp_name'][$index]}<br>";
		if (!is_file($imgarray['tmp_name'][$index]))
			continue;
			
		$newfile = $imgdir.$nameprefix.$savecount.substr(strrchr($imgarray['name'][$index], "."), 0);
#		echo "new file:{$newfile}<br>";
		if (copy($imgarray['tmp_name'][$index], $newfile)) {
			++$savecount;
			$imgfilenames.=basename($newfile).";";
		}
		else{
			--$savecount;
#   		echo "failed to copy {$imgarray['tmp_name'][$index]}...\n";
		}
   }
   
   return $imgfilenames;
}
function saveaddimages(&$imgarray,$nameprefix, $imgdir)
{
	$imgdir = setslash($imgdir);
	if (!file_exists($imgdir))
	if (!mkdir($imgdir))
	{
#		echo "cant create dir:{$imgdir}<br>";
		return FALSE;
	}
	
#	deletefiles($imgdir.$nameprefix."*");
	
	$imgfilenames = "";
	$filecount = count($imgarray['name']);
	$savecount = 0;
	$imgindex = 0;
	for ($index = 0; $index < $filecount; ++$index){
#		echo "see tmp:{$imgarray['tmp_name'][$index]}<br>";
		if (!is_file($imgarray['tmp_name'][$index]))
			continue;
			
		while($imgindex < 20)
		{
			$newfile = $imgdir.$nameprefix.$imgindex.substr(strrchr($imgarray['name'][$index], "."), 0);
			if (!file_exists($newfile))
				break;

			++$imgindex;
		}
#		echo "new file:{$newfile}<br>";
		if (copy($imgarray['tmp_name'][$index], $newfile)) {
			++$savecount;
			$imgfilenames.=basename($newfile).";";
		}
		else{
			--$savecount;
#   		echo "failed to copy {$imgarray['tmp_name'][$index]}...\n";
		}
   }
   
   return $imgfilenames;
}

function uniquefilename()
{
    list($usec, $sec) = explode(" ", microtime());
    return ( $sec."_".((int)($usec*1000)) );
}
?>