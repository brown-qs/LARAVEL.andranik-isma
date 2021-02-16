<?php

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
  for($i = 0; $i < 64 - $len; $i++)
    $h_word_bits = "0".$h_word_bits;

  $len = strlen($l_word_bits);
  for($i = 0; $i < 64 - $len; $i++)
    $l_word_bits = "0".$l_word_bits;


  if($bit_pos < 64)
    $l_word_bits[64 - $bit_pos - 1] = "1";
  else
    $h_word_bits[64 - ($bit_pos - 64) - 1] = "1";
  $h_word = unfucked_base_convert($h_word_bits, 2, 10);
  $l_word = unfucked_base_convert($l_word_bits, 2, 10);

}

function word128_bit_check($h_word, $l_word, $bit_pos)
{
  $h_word_bits = unfucked_base_convert($h_word, 10, 2);
  $l_word_bits = unfucked_base_convert($l_word, 10, 2);
  $len = strlen($h_word_bits);
  for($i = 0; $i < 64 - $len; $i++)
    $h_word_bits = "0".$h_word_bits;
     echo  $h_word_bits."<br/>";

  $len = strlen($l_word_bits);
  for($i = 0; $i < 64 - $len; $i++)
    $l_word_bits = "0".$l_word_bits;

  if($bit_pos < 64)
  { if($l_word_bits[64 - $bit_pos - 1] == "1")
      return true;
    else
      return false;
  }
  else
  { 

   if($h_word_bits[64 - ($bit_pos - 64) - 1] == "1")
      return true;
    else
      return false;
  }
}


$h_word = 0; 
$l_word = 0;
word128_and($h_word, $l_word, 67);


$str = word128_bit_check($h_word, $l_word, 67);

echo "hWord = {$h_word} , lword = {$l_word}, type = {$str}";

?>