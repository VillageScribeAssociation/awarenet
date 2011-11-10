<?

	require_once($kapenta->installPath . 'core/kcron.class.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/client.class.php');

//--------------------------------------------------------------------------------------------------
//*	worker action which runs after cron (singleton)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	set up the worker
	//----------------------------------------------------------------------------------------------
	$start = $kapenta->time();
	$UID = $kapenta->createUID();

	if (false == isset($cron)) { $cron = new KCron(); }

	$registry->set('p2p.worker', $UID);
	$continue = true;
	if ('yes' != $registry->get('p2p.enabled')) { $continue = false; }

	$maxIterations = 100;				//TODO: registry setting

	//----------------------------------------------------------------------------------------------
	//	start output if printing to con
	//----------------------------------------------------------------------------------------------
	if ('admin' == $user->role) { echo $theme->expandBlocks('[[:theme::ifscrollheader:]]', ''); }
	echo "<b>Worker process:</b> $UID<br/>\n"; flush();

	//----------------------------------------------------------------------------------------------
	//	get list of open peers from the database
	//----------------------------------------------------------------------------------------------
	$peers = $db->loadRange('p2p_peer', '*', array("firewalled='no'"));

	//----------------------------------------------------------------------------------------------
	//	check database for new gifts every 10 seconds
	//----------------------------------------------------------------------------------------------
	while(true == $continue) {
		echo "starting cycle...<br/>\n";
		//------------------------------------------------------------------------------------------
		//	look for any gifts we might give to peers
		//------------------------------------------------------------------------------------------
		$conditions = array("(status='want' OR status='waiting')");
		$num = $db->countRange('p2p_gift', '*', $conditions);
		if ($num > 0) {
			//--------------------------------------------------------------------------------------
			//	available gifts, send them
			//--------------------------------------------------------------------------------------
			$cron->log('We have outgoing items to send...');
	
			foreach($peers as $peer) {
				if ('no' == $peer['firewalled']) {
					$client = new P2P_Client($peer['UID']);
					$conditions = array();
					$conditions[] = "(status='want' OR status='waiting')";
					$conditions[] = "peer='" . $db->addMarkup($peer['UID']) . "'";
					$outstanding = $db->countRange('p2p_peer', '*', $conditions);

					echo "Checking with peer: " . $peer['name'] . "<br/>\n"; flush();

					if ($outstanding > 0) {
						echo "p2p.worker: " . $registry->get('p2p.worker', true) . "<br/>\n"; flush();
						if ($UID == $registry->get('p2p.worker', true)) {
							echo "p2p.worker: cleant->push()<br/>\n"; flush();
							$report = $client->push();
							$cron->log($report, 'black');
							if ('admin' == $user->role) { echo $report; }
						}	

						echo "p2p.worker: " . $registry->get('p2p.worker', true) . "<br/>\n"; flush();
						if ($UID == $registry->get('p2p.worker', true)) {
							echo "p2p.worker: cleant->pull()<br/>\n"; flush();
							$report = $client->pull();
							$cron->log($report, 'black');
							if ('admin' == $user->role) { echo $report; }
						}

						echo "p2p.worker: " . $registry->get('p2p.worker', true) . "<br/>\n"; flush();
						if ($UID == $registry->get('p2p.worker', true)) {
							echo "p2p.worker: cleant->pushFiles()<br/>\n"; flush();
							$report = $client->pushFiles();
							$cron->log($report, 'black');
							if ('admin' == $user->role) { echo $report; }
						}

						echo "p2p.worker: " . $registry->get('p2p.worker', true) . "<br/>\n"; flush();
						if ($UID == $registry->get('p2p.worker', true)) {
							echo "p2p.worker: cleant->pullFiles()<br/>\n"; flush();
							$report = $client->pullFiles();
							$cron->log($report, 'black');
							if ('admin' == $user->role) { echo $report; }
						}
					} // end if outstanding > 0
				} // end if not firewalled
			} // end foreach peer

		} // end if num > 0

		//------------------------------------------------------------------------------------------
		//	die if another worker starts or if we have reached max iterations
		//------------------------------------------------------------------------------------------
		if ($UID == $registry->get('p2p.worker', true)) {
			$msg = 'No other workers found...';
			$cron->log($msg, 'green');
			echo $msg; flush();

		} else {
			$continue = false;
			$msg = 'Detected new worker thread, shutting down...';
			$cron->log($msg, 'black');
			echo $msg; flush();
			break;
		}

		if (0 == $maxIterations) { $continue = false; }
		$maxIterations--;

		//------------------------------------------------------------------------------------------
		//	sleep for ten seconds
		//------------------------------------------------------------------------------------------
		echo "Sleeping: ";
		for ($i = 0; $i < 10; $i++) { echo '.'; sleep(1); flush(); }
		echo "..."; flush();

		//------------------------------------------------------------------------------------------
		//	timeout after ten minutes
		//------------------------------------------------------------------------------------------
		$now = $kapenta->time();
		if (($now - $start) > (600)) { $continue = false; }
	} // end while

	$msg = "End of worker cycle..."; flush();
	$cron->log($msg, 'black');
	if ('admin' == $user->role) { 
		echo $msg; 	
		echo $theme->expandBlocks('[[:theme::ifscrollheader:]]', ''); 
	}

?>
