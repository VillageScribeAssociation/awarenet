<?

//-------------------------------------------------------------------------------------------------
//	test of fork function
//-------------------------------------------------------------------------------------------------

if (array_key_exists('data', $req->args) == true) { 

	for ($i = 0; $i < 10; $i++) {

		//---------------------------------------------------------------------------------------------
		//	send dummy data, perform time-consuing operation
		//---------------------------------------------------------------------------------------------
		$fileName = $installPath . 'modules/sync/test.txt';
		$fh = fopen($fileName, 'w+');
		fwrite($fh, "script called<br/>\n");
		fclose($fh);

		echo "data requested<br/>\n"; 
		for ($i = 0; $i < 30; $i++) {
			echo "tick $i <br/>\n"; 
			$fh = fopen($fileName, 'a+');
			fwrite($fh, "tick $i <br/>\n");
			fclose($fh);
			sleep(1); 
		}

	}

} else {

	$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	echo "created socket<br/>\n"; flush();

	$result = socket_bind($sock, '127.0.0.1');
	if (true == $result) { echo "bound socket<br/>\n"; flush();	} 
	else { echo "could not bind socket<br/>\n"; flush(); }

	$result = socket_connect($sock, '127.0.0.1', 80);

	if (true == $result) { echo "connected socket<br/>\n"; flush(); }
	else { echo "could not connect socket<br/>\n"; flush(); }

	$send = "GET /sync/forktest/data_yes/ HTTP/1.0\n"
		  . "Host: awarenet.co.za\n\n";

	$result = socket_send($sock, $send, strlen($send), 0);
	if ($result > 0) { echo "data sent... ($result bytes)<br/>\n"; flush(); }
	else { echo "could not send data <br/>\n"; flush(); }

//	$reply = "";
//
//     $recv = "";
//     $recv = socket_read($sock, '100');
//     if($recv != "") { 
//		$reply .= $recv; 
//		echo "data recieved: $recv <br/>\n"; flush();
//	}

	socket_close($sock);

	echo "finished, ending script<br/>\n";

}

?>
