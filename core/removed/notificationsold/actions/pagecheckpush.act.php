<?

//-------------------------------------------------------------------------------------------------
//	check to see if a page has any new notices
//-------------------------------------------------------------------------------------------------

	$sleepTime = 1;
	$numCycles = 30;	// five minutes

	require_once($kapenta->installPath . 'modules/notifications/models/pageclient.mod.php');
	if ('' == $req->ref) { die(); }

	if (strpos(' ' . $req->ref, 'Array') != false) 
		{ logErr('notifications', 'pagecheck', 'Array in UID');	}	// having problem with this

	logErr('notifications', 'pagecheckpush', 'check start');


	header('Access-Control-Allow-Origin: *');

	//---------------------------------------------------------------------------------------------
	//	repeatedly check every x ms
	//---------------------------------------------------------------------------------------------	

	echo "#INBOX " . $model->UID . "\n";
	$model = new PageClient($req->ref);
	/*
	for ($i = 0; $i < $numCycles; $i++) {
		echo "#NOOP\n";
		//-----------------------------------------------------------------------------------------
		//	send noop every 30 seconds
		//-----------------------------------------------------------------------------------------
		if (($i % 5) == 0) { echo "#NOOP\n"; }

		//-----------------------------------------------------------------------------------------
		//	load the client and check for messages
		//-----------------------------------------------------------------------------------------

		$model = new PageClient($req->ref);
		if ($model->inbox != '') { 
			echo $model->inbox;
			$model->inbox = '';	
			$model->save();
			break; // client processes messages
		}

		sleep($sleepTime);

	}
	*/
	//---------------------------------------------------------------------------------------------
	//	update the timestamp if record is getting old
	//---------------------------------------------------------------------------------------------
	if ($model->old == true) { $model->updateTimeStamp(); }

	//---------------------------------------------------------------------------------------------
	//	clear out any dead page clients (have become too old/not checked)
	//---------------------------------------------------------------------------------------------
	$model->bringOutYourDead();
	echo "\n#ENDOFMESSAGES (" . ($t2 - $t1) . " microseconds)\n";
	
	logErr('notifications', 'pagecheckpush', 'check complete');

?>
