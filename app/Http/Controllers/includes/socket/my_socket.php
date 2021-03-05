<?php
############################################################################
function my_socket_connect($address, $port)
{

	# Create a TCP/IP socket. 
	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if ($socket == false) {
	#    echo "socket_create() failed.<br>Reason: ($result) " . socket_strerror(socket_last_error()) . "<br>";
			//file_put_contents("trans_error.log","[".date("c")."]"."socket_create() failed.<br>Reason: ($socket) Code:".socket_last_error($socket)." . msg:". socket_strerror(socket_last_error()) . "<br>\n\n",FILE_APPEND);
			//file_put_contents("trans_error.log","***************************************************************************************\n\n",FILE_APPEND);

    	return false;
	}

	$result = socket_connect($socket, $address, $port);
	if ($result === false) {
	  #echo "socket_connect() failed.<br>Reason: ($result) Code:".socket_last_error($socket)." . msg:". socket_strerror(socket_last_error($socket)) . "<br>";
	  //file_put_contents("trans_error.log","socket_connect() failed. Reason: ($result) Code:".socket_last_error($socket)." . msg:". socket_strerror(socket_last_error($socket)) . "\r\n",FILE_APPEND);
		socket_close($socket);
		return false;
	} 
	return $socket;
}
############################################################################
function my_socket_write_str($socket, $str)
{
	$write_length  = socket_write($socket, $str, strlen($str));
	if ($write_length  === false)
	{
	#   	 echo "socket_write() failed.<br>Reason: ($result) " . socket_strerror(socket_last_error($socket)) . "<br>";
		return false;
	}	
	
	return $write_length;
}
############################################################################
function my_socket_read_nonblock2($socket, $max_length, $time_out, $repeat_time)
{
	$curr_len = -1;
	$real_len = 0;
	$firstCall = true;
	$retStr = "";
	//$rcv_len = 80;
	while($curr_len < $real_len)
	{		
   	  $out_text = "";
	  $from = "";
  	  $port = 0;
          $len = socket_recvfrom($socket, $out_text, $max_length, 0, $from, $port);
          
          if($firstCall)
          { $trans_header_len = strpos($out_text,"\n");
            $pos1 = strpos($out_text,"body-length:");
            $pos2 = strpos($out_text,";",$pos1);
            $body_len = substr($out_text, $pos1+12, $pos2 - $pos1 - 12);

            $firstCall = false;
            $real_len = $trans_header_len+$body_len;
            $curr_len = 0;
          }
          $curr_len += $len;
          $retStr = $retStr.$out_text;
        }
          //  
     ///   file_put_contents("1.log",$retStr);
        return $retStr;

}
############################################################################
function my_socket_read_nonblock($socket, $max_length, $time_out/*sec*/, $repeat_time/*sec*/)
{
		
			$from = "";
			$port = 0;
            //socket_recvfrom($socket, $out_text, $max_length, MSG_WAITALL, $from, $port);
             $out_text  =  my_socket_read_nonblock2($socket, $max_length, $time_out, $repeat_time);
             return  $out_text;
	if ($repeat_time == 0)
		$repeat_time = 1;

	if ($time_out < $repeat_time)
		$repeat_time = $time_out;
		
	$wait_time = 0;
		
	$read1 = array($socket);
	
	$out_text = "";
	while ( true )
	{
		$read = $read1;
		# select socket $repeat_time long?
		if (($time_out - $wait_time) < $repeat_time)
			$repeat_time = abs(($time_out - $wait_time));
			
		$write = NULL; $except = NULL;
		$num_changed_sockets = socket_select($read, $write, $except, $repeat_time);
		$wait_time += $repeat_time;
		if (socket_last_error($socket) && $num_changed_sockets > 0)
		{
			# echo for detect client connection
			echo "         ";
			flush();

			return false;
		}
	
		# can read from socket?
		if ($num_changed_sockets && socket_last_error($socket) == 0)
		{
			return "zzzzzz";
			$from = "";
			$port = 0;
            //socket_recvfrom($socket, $out_text, $max_length, MSG_WAITALL, $from, $port);
             $out_text  =  my_socket_read_nonblock2($socket, $max_length, $time_out, $repeat_time);
            //socket_recv($socket, $out_text, $max_length, MSG_PEEK);

			# echo for detect client connection
//			echo "         ";
			flush();
			return $out_text;
		}

			return "zzzzzz";
		# echo for detect client connection
//		echo "         ";
		flush();

		if ($wait_time >= $time_out)
			break;
	}

	return $out_text;
}
############################################################################
function my_socket_close($socket)
{
	socket_close($socket);
}
?>