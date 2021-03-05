<?php
function GetIProto($packet)
{
  $header = pack("cccsNcN", 0x2A, 0, 0, 0, strlen($packet) + 5, 1, strlen($packet));
  $header = $header.$packet;
  return $header;

}

function hex2bin1($hexdata) {
	
  #$strdata = '';
  $bindata = "";
  for ($i=0;$i<strlen($hexdata);$i+=2) {
     $bindata.= chr(hexdec(substr($hexdata,$i,2)));
  }  
  return $bindata;
}

function ArmAnsiToUTF8( &$ansistr )
{
$AnsiToUTF_8_Array = array(
"a7",    //0x00A7 - Armenian Section Sign             
"d689",  //0x0589 - Armenian Full Stop (Verjaket)     
"29",    //0x0029 - Armenian Right Parenthesis        
"28",    //0x0028 - Armenian Left Parenthesis         
"d59a",  //0x055A - Armenian Right Quotation Mark     
"d599",  //0x0559 - Armenian Left Quotation Mark      
"d687",  //0x0587 - Armenian Ligature "ew"            
"2e",    //0x002E - Armenian Dot (Mijaket)            
"d59d",  //0x055D - Armenian Separation Mark (But)    
"2c",    //0x002C - Armenian Comma                    
"2d",    //0x002D - Armenian EN Dash                  
"2d",    //0x002D - Armenian Hyphen (Yentamna)        
"2e",    //0x002E - Armenian Ellipsis (...)           
"d59c",  //0x055C - Armenian Exclamation Mark (Amanak)
"d59b",  //0x055B - Armenian Accent (Shesht)          
"d59e",  //0x055E - Armenian Question Mark (Paruyk)   
"d4b1",  //0x0531 - Armenian Capital Letter [ayb] 
"d5a1",  //0x0561 - Armenian Small Letter [ayb]   
"d4b2",  //0x0532 - Armenian Capital Letter [ben] 
"d5a2",  //0x0562 - Armenian Small Letter [ben]  
"d4b3",  //0x0533 - Armenian Capital Letter [gim]
"d5a3",  //0x0563 - Armenian Small Letter [gim]  
"d4b4",  //0x0534 - Armenian Capital Letter [da] 
"d5a4",  //0x0564 - Armenian Small Letter [da] 
"d4b5",  //0x0535 - Armenian Capital Letter [yech]
"d5a5",  //0x0565 - Armenian Small Letter [yech]  
"d4b6",  //0x0536 - Armenian Capital Letter [za]  
"d5a6",  //0x0566 - Armenian Small Letter [za]    
"d4b7",  //0x0537 - Armenian Capital Letter [e]   
"d5a7",  //0x0567 - Armenian Small Letter [e]     
"d4b8",  //0x0538 - Armenian Capital Letter [at]  
"d5a8",  //0x0568 - Armenian Small Letter [at]    
"d4b9",  //0x0539 - Armenian Capital Letter [to]  
"d5a9",  //0x0569 - Armenian Small Letter [to]    
"d4ba",  //0x053A - Armenian Capital Letter [zhe] 
"d5aa",  //0x056A - Armenian Small Letter [zhe]   
"d4bb",  //0x053B - Armenian Capital Letter [ini] 
"d5ab",  //0x056B - Armenian Small Letter [ini]   
"d4bc",  //0x053C - Armenian Capital Letter [lyun]
"d5ac",  //0x056C - Armenian Small Letter [lyun]  
"d4bd",  //0x053D - Armenian Capital Letter [khe] 
"d5ad",  //0x056D - Armenian Small Letter [khe]   
"d4be",  //0x053E - Armenian Capital Letter [tsa] 
"d5ae",  //0x056E - Armenian Small Letter [tsa]   
"d4bf",  //0x053F - Armenian Capital Letter [ken] 
"d5af",  //0x056F - Armenian Small Letter [ken]   
"d580",  //0x0540 - Armenian Capital Letter [ho]  
"d5b0",  //0x0570 - Armenian Small Letter [ho]    
"d581",  //0x0541 - Armenian Capital Letter [dza] 
"d5b1",  //0x0571 - Armenian Small Letter [dza]   
"d582",  //0x0542 - Armenian Capital Letter [ghat] 
"d5b2",  //0x0572 - Armenian Small Letter [ghat]  
"d583",  //0x0543 - Armenian Capital Letter [tche] 
"d5b3",  //0x0573 - Armenian Small Letter [tche]  
"d584",  //0x0544 - Armenian Capital Letter [men] 
"d5b4",  //0x0574 - Armenian Small Letter [men]   
"d585",  //0x0545 - Armenian Capital Letter [hi]  
"d5b5",  //0x0575 - Armenian Small Letter [hi]    
"d586",  //0x0546 - Armenian Capital Letter [nu]  
"d5b6",  //0x0576 - Armenian Small Letter [nu]    
"d587",  //0x0547 - Armenian Capital Letter [sha] 
"d5b7",  //0x0577 - Armenian Small Letter [sha]   
"d588",  //0x0548 - Armenian Capital Letter [vo]  
"d5b8",  //0x0578 - Armenian Small Letter [vo]    
"d589",  //0x0549 - Armenian Capital Letter [cha]  
"d5b9",  //0x0579 - Armenian Small Letter [cha]   
"d58a",  //0x054A - Armenian Capital Letter [pe]  
"d5ba",  //0x057A - Armenian Small Letter [pe]  
"d58b",  //0x054B - Armenian Capital Letter [je]  
"d5bb",  //0x057B - Armenian Small Letter [je]    
"d58c",  //0x054C - Armenian Capital Letter [ra]  
"d5bc",  //0x057C - Armenian Small Letter [ra]    
"d58d",  //0x054D - Armenian Capital Letter [se]  
"d5bd",  //0x057D - Armenian Small Letter [se]    
"d58e",  //0x054E - Armenian Capital Letter [vev] 
"d5be",  //0x057E - Armenian Small Letter [vev]   
"d58f",  //0x054F - Armenian Capital Letter [tyun] 
"d5bf",  //0x057F - Armenian Small Letter [tyun]  
"d590",  //0x0550 - Armenian Capital Letter [re]  
"d680",  //0x0580 - Armenian Small Letter [re]    
"d591",  //0x0551 - Armenian Capital Letter [tso] 
"d681",  //0x0581 - Armenian Small Letter [tso]   
"d592",  //0x0552 - Armenian Capital Letter [vyun] 
"d682",  //0x0582 - Armenian Small Letter [vyun]  
"d593",  //0x0553 - Armenian Capital Letter [pyur] 
"d683",  //0x0583 - Armenian Small Letter [pyur]  
"d594",  //0x0554 - Armenian Capital Letter [ke]  
"d684",  //0x0584 - Armenian Small Letter [ke]    
"d595",  //0x0555 - Armenian Capital Letter [o]   
"d685",  //0x0585 - Armenian Small Letter [o]     
"d596",  //0x0556 - Armenian Capital Letter [fe]  
"d686",  //0x0586 - Armenian Small Letter [fe]    
"27"     //0x0027 - Armenian Apostrophe
);        

        $length = strlen($ansistr);
        $new_str = "";
        for( $si = 0; $si < $length; $si = $si+1)
        {
                if(ord($ansistr[$si]) >= 0xA2 && ord($ansistr[$si]) <= 0xFE)
                {
                        $new_str.= hex2bin($AnsiToUTF_8_Array[ord($ansistr[$si]) - 0xA2]); 
                }
                else
                {
                        $new_str.=$ansistr[$si];
                }
        }
                
        return $new_str;
}


function get_language($str)
{
	$midle_code = 0;
	$length = strlen($str);
	for( $si = 0; $si < $length; $si = $si+1)
	{
		$midle_code += ord($str[$si]);
	}
	
	$midle_code = $midle_code/$length;
	if ($midle_code > 128)
		return 2;#amenian
		
	return 1;#english
}

#----------------------------------------------------------------------
function getParamsArray1($str, $separators = ";")
{
//	echo $lev1str;
	$ParamArray = array();
	$ParamArraySize = 0;

	$tok = strtok($str,$separators);
	while($tok !== FALSE)
	{
		$ParamArraySize++;
		$ParamArray = array_pad($ParamArray,$ParamArraySize,$tok);

		$tok = strtok($separators);
	}

	return $ParamArray;	
}

function getParamsArray2($lev1str, $lev1separator,  $lev2separator)
{
//	echo $lev1str;
	$ParamArray = array();
	$lev1partcount = 0;

	$lev1offset = 0;
	$lev1len = strpos($lev1str,$lev1separator,$lev1offset);
	while($lev1len !== FALSE)
	{
		$lev1len -= $lev1offset;
		$lev2str = substr($lev1str,$lev1offset,$lev1len);

		$lev2ParamArray = array();
		$lev2partcount = 0;
		
		$lev2offset = 0;
		$lev2len = strpos($lev2str,$lev2separator,$lev2offset);
		while ($lev2len !==FALSE) {

			$lev2len -= $lev2offset;
			$lev2part = substr($lev2str,$lev2offset,$lev2len);

			$lev2partcount++;
			$lev2ParamArray = array_pad($lev2ParamArray,$lev2partcount,$lev2part);

			$lev2offset += $lev2len+1;
			$lev2len = strpos($lev2str,$lev2separator,$lev2offset);
		}

		$lev1partcount++;
		$ParamArray = array_pad($ParamArray,$lev1partcount,$lev2ParamArray);

		$lev1offset += $lev1len+1;
		$lev1len = strpos($lev1str,$lev1separator,$lev1offset);
	}

	return $ParamArray;	
}

function cut_useful_text($_str, $cnt = 50)
{
	$pos = 0;
	$str = $_str;
	$tok = strtok($str, " .:;,|\r\n\t");
	while ($tok !== false && $cnt > 0) 
	{
		$tok = strtok(" .:;,|\r\n\t");
		$pos = strpos($_str,$tok,$pos);
		--$cnt;
	}
	if($pos == 0)
		$pos = strlen($_str);
	$rv = substr($_str, 0, $pos);
	
	return $rv;
}

?>
