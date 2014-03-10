<?php

	require_once($kapenta->installPath . 'modules/p2p/inc/dispatcher.class.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/updates.class.php');

//--------------------------------------------------------------------------------------------------
//*	Object to manage scheduled P2P tasks
//--------------------------------------------------------------------------------------------------

class P2P_Worker {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $UID;						//_	UID of this worker processs [string]
	var $maxAge = 900;				//_	maximum age of worker process, seconds [int]
	var $pidFile = '';				//_	process file for this worker [string]
	var $chatty = false;			//_ set to true to enable test / debug output [bool]
	var $active = false;			//_	do we actively connect to other peers? [string]
	var $startTime = 0;				//_	time this worker was started [int]

	var $dispatcher;				//_	event dispatcher [object]

	var $peers;						//_	array of serialized P2P_Peer objects [array]

	var $maxDispatchInterval = 30;	//_	maximum time between dispact cycles (seconds) [int]
	var $maxProcessInterval = 20;	//_	maximum time between checking other threads [int]
	var $maxPullInterval = 300;		//_	maximum time between pulling for updates [int]
	var $maxPshInterval = 60;		//_	maximum time between checking and pushing updates [int]

	var $dispatchInterval = 3;		//_	current time to wait between dispatching events [int]
	var $processInterval = 5;		//_	current time to wait between checking other workers [int]
	var $pullInterval = 5;			//_	current time to wait before pulling (seconds) [int]
	var $pushInterval = 5;			//_	current time to wait before pushing (seconds) [int]

	var $step = 5;					//_	stepwise backoff (seconds) [int]
	var $eventCount = 10;			//_	number of events to dispatch per cycle [int]

	var $dispatchTime = 0;			//_	time at which events should be checked [int]
	var $processTime = 0;			//_	time at which we check for other workers / own expiry [int]
	var $pullTime = 0;				//_	time to pull from other peers [int]
	var $pushTime = 0;				//_	time to push to other peers [int]

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function P2P_Worker($chatty = false) {
		global $kapenta;
		global $kapenta;
		global $kapenta;

		//	initialize self
		$this->UID = $kapenta->createUID();
		$this->chatty = $chatty;
		$this->startTime = $kapenta->time();
		$this->dispatcher = new P2P_Dispatcher();

		$this->peers = $kapenta->db->loadRange('p2p_peer', '*');

		$this->eventCount = (int)$kapenta->registry->get('p2p.batchsize');
		if (0 == $this->eventCount) { $this->eventCount = 10; }

		//	write the PID file
		$this->pidFile = 'data/p2p/worker/' . $this->UID . '.pid';
		$kapenta->fs->put($this->pidFile, $kapenta->time());

		$workers = $this->listWorkers();
		
		$this->dispatchTime = $this->startTime;
		$this->processTime = $this->startTime;
		$this->pullTime = $this->startTime;
		$this->pushTime = $this->startTime;

		if ('active' == $kapenta->registry->get('p2p.mode')) { $this->active = true; }

		//------------------------------------------------------------------------------------------
		//	log this
		//------------------------------------------------------------------------------------------
		$now = $kapenta->time();
		$msg = ''
		 . "$now Starting new worker process, PID := " . $this->UID . "\n"
		 . "$now Active HTTP Client: " . ($this->active ? 'yes' : 'no') . "\n";

		$kapenta->logEvent('worker', 'p2p', 'P2P_Worker', $msg);

		$kapenta->registry->set('p2p.started', $kapenta->datetime());

		if ($chatty) { echo $msg; }
	}

	//----------------------------------------------------------------------------------------------
	//.	main process loop
	//----------------------------------------------------------------------------------------------

	function work() {
		global $kapenta;
		$continue = true;

		while (true == $continue) {
			//--------------------------------------------------------------------------------------
			//	first check if this process has aged out
			//--------------------------------------------------------------------------------------
			$age = $this->startTime - $kapenta->time();
			if ($age > $this->maxAge) { $continue = false; }

			//--------------------------------------------------------------------------------------
			//	if time to dispatch
			//--------------------------------------------------------------------------------------
			$now = $kapenta->time();

			if ($now >= $this->dispatchTime) {
				$this->log("Dispatching events.");
				$this->dispatch();
				$now = $kapenta->time();
				$this->dispatchTime = ($now + $this->dispatchInterval);

				$this->log("Scheduled next dispatch for " . $this->dispatchTime);
			}

			//--------------------------------------------------------------------------------------
			//	if time to check PIDs
			//--------------------------------------------------------------------------------------
			$now = $kapenta->time();
			if ($now >= $this->processTime) {
				$this->log("Maintaining process list.");
				$this->listWorkers();

				$now = $kapenta->time();
				$this->processTime = ($now + $this->processInterval);
				$this->log("Next PID census scheduled for " . $this->processTime);
			}
			
			//--------------------------------------------------------------------------------------
			//	if time to push, and we are in active mode
			//--------------------------------------------------------------------------------------
			$now = $kapenta->time();
			if ((true == $this->active) && ($now >= $this->pushTime)) {
				$this->log("Pushing to other clients.");

				$this->push();

				$now = $kapenta->time();
				$this->pushTime = $now + $this->pushInterval;

				$this->log("Next push scheduled for " . $this->pushTime);
			}

			//--------------------------------------------------------------------------------------
			//	if time to pull, and we are in active mode
			//--------------------------------------------------------------------------------------
			$now = $kapenta->time();
			if ((true == $this->active) && ($now >= $this->pullTime)) {
				$this->log("Polling peers for updates.");
				$this->pull();

				$now = $kapenta->time();
				$this->pullTime = $now + $this->pullInterval;
				$this->log("Next pull scheduled for " . $this->pullTime);
			}

			//--------------------------------------------------------------------------------------
			//	done with this cycle, sleep until next scheduled event
			//--------------------------------------------------------------------------------------
			$now = $kapenta->time();
			$next = $this->nextEventTime();
			if ($next > $now) {
				$this->log("Sleeping until $next.");
				sleep($next - $now);
			} else {
				$this->log("Not sleeping, there are outstanding events since $next.");
			}
		}
	
		$this->log("exiting process, end of life.");
	}

	//----------------------------------------------------------------------------------------------
	//.	get time of next scheduled event
	//----------------------------------------------------------------------------------------------

	function nextEventTime() {
		if (true == $this->active) {
			return min($this->dispatchTime, $this->processTime, $this->pullTime, $this->pushTime);
		} else {
			return min($this->dispatchTime, $this->processTime);
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	list all worker processes
	//----------------------------------------------------------------------------------------------
	//returns: set of worker UIDs start times [array]

	function listWorkers() {
		global $kapenta;
		$workers = array();	
		$changed = false;				//%	do we need to reload workes after modifying PID files?

		$files = $kapenta->listFiles('data/p2p/worker/', '.pid');

		foreach($files as $file) {
			$startTime = (int)$kapenta->fs->get('data/p2p/worker/' . $file);
			$workers['data/p2p/worker/' . $file] = $startTime;
		}

		//------------------------------------------------------------------------------------------
		//	ask any workers older than we are to quit
		//------------------------------------------------------------------------------------------
		foreach($workers as $file => $startTime) {
			if ($this->startTime > $startTime) {
				$kapenta->fileDelete($file, true);			//	send request to stop
				$changed = true;

				if (true == $this->chatty) {
					$this->log("Retiring: $startTime > " . $this->startTime);
					$this->log("Sent shutdown request to process: $file");
				}
			}
		}

		//------------------------------------------------------------------------------------------
		//	delete expired pid files while we're at it
		//------------------------------------------------------------------------------------------
		foreach($workers as $file => $startTime) {
			$now = $kapenta->time();
			if (($now - $startTime) > $this->maxAge) {
				$kapenta->fileDelete($file, true);			//	send request to stop
				$changed = true;

				$this->log("StartTime: $startTime Now: $now Age: " . (string)($now - $startTime));
				$this->log("Sent shutdown request to: $file");
			}
		}

		if (true == $changed) {
			$this->log("Reloading worker list...");
			$workers = $this->listWorkers();
		}

		//------------------------------------------------------------------------------------------
		//	if own PID file is deleted then it is time to quit
		//------------------------------------------------------------------------------------------
		if (false == array_key_exists($this->pidFile, $workers)) {
			$this->log("Shutdown request received, stopping worker process.");
			die();
		}

		$this->log(count($workers) . " active workers.");

		return $workers;
	}

	//----------------------------------------------------------------------------------------------
	//.	dispatch n queued events
	//----------------------------------------------------------------------------------------------
	//returns: true if events dispatched, false if all queues empty [bool]


	function dispatch() {
		global $kapenta;

		$count = $this->dispatcher->dispatch($this->eventCount);

		if (0 == $count) { $this->dispatchInterval += $this->step; } 	//	back off progressively
		else { $this->dispatchInterval = 0; }							//	re-check immediately

		if ($this->dispatchInterval > $this->maxDispatchInterval) {
			$this->dispatchInterval = $this->maxDispatchInterval;
		}

		$now = $kapenta->time();
		$this->log("dispatched: $count events max: " . $this->eventCount);

		return ($count > 0);
	}

	//----------------------------------------------------------------------------------------------
	//.	poll non-firewalled peers for updates
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function pull() {
		global $kapenta;
		if (false == $this->active) { return false; }

		$this->log("Active polling of " . count($this->peers) . " peers.");
		$dataReceived = false;

		foreach($this->peers as $peer) {
			if ('no' == $peer['firewalled']) {
				$now = $kapenta->time();
				$this->log("Polling: " . $peer['name']);
			
				$model = new P2P_Peer($peer['UID']);
				$msg = $model->getUpdates();
				$now = $kapenta->time();

				if (0 == count($msg)) {
					$this->log("$now Queue empty.");
				} else {
					if ((true == $this->chatty) && ('' != $msg['file'])) {
						$logMsg = "Packet\n"
						 . "    file: " . $msg['file'] . "\n"
						 . "    priority: " . $msg['priority'] . "\n"
						 . "    base: " . basename($msg['file']) . "\n"
						 . "    sig: " . basename($msg['signature']) . "\n"
						 . "    raw: " . strlen($msg['raw']) . " bytes\n";
						$this->log($logMsg);
					}

					if ('yes' == $msg['verified']) {
						$dataReceived = true;
						$this->log("Signature verified, parsing updates...");
						$updates = new P2P_Updates($model->UID);
						$count = $updates->explode($msg['message'], $msg['priority'], $model->UID);

						$now = $kapenta->time();
						$this->log("Acknowledging $count updates...");

						$response = $model->ackUpdates($msg['file']);

						$this->log("Peer responds:\n$response");

						if (0 == $count) {
							//	nothing received, wait a little longer next time, up to a point
							$this->pullInterval += $this->step;
							if ($this->pullInterval > $this->maxPullInterval) {
								$this->pullInterval = $this->maxPullInterval;
							}
						} else {
							//	there might be more stuff in this queue, re-poll immediately
							$this->pullInterval = 0;
						}

					} // end if packet verified

				} // end if packet received

			} // end if firewalled

		} // end foreach peer

		return $dataReceived;
	}

	//----------------------------------------------------------------------------------------------
	//.	push updates to non-firewalled peers
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function push() {
		global $kapenta;
		global $kapenta;		
		global $utils;

		$dataSent = false;

		foreach($this->peers as $peerAry) {
			if ('no' == $peerAry['firewalled']) {

				$this->log('Push to ' . $peerAry['name'] . ' (' . $peerAry['UID'] . ')');

				$peer = new P2P_Peer($peerAry['UID']);
				if (false == $peer->loaded) {
					$this->log('Could not load peer.');
					return false;
				}

				$updates = new P2P_Updates($peer->UID);

				$myUID = $kapenta->registry->get('p2p.server.uid');
				if ('' == $myUID) { $kapenta->page->doXmlError('this peer does not have a UID'); }

				//----------------------------------------------------------------------------------
				//	first look for any locked files, by priority (complete batches, more efficent)
				//----------------------------------------------------------------------------------
				$files = $updates->listFiles();
				$sent = false;

				for ($priority = 0; $priority < 10; $priority++) {
					foreach($files as $fileName => $meta) {
						if (($priority == (int)$meta['priority']) && (false == $sent)) {
				
							//----------------------------------------------------------------------
							//	have found a file to send, lock it if not already done
							//----------------------------------------------------------------------
							if ('no' == $meta['locked']) {
								$updates->lockFile($fileName);
								$fileName = str_replace('.xml.txt', '.xml.bz2', $fileName);
							}

							//----------------------------------------------------------------------
							//	encrypt with this peer's public key and send it
							//----------------------------------------------------------------------
							$raw = $kapenta->fs->get($fileName);
							$encrypted = $peer->pack($raw, $fileName);;

							$url = $peer->url . 'p2p/updatesfrom/' . $myUID;
							$this->log("pushing to: $url");
							$this->log("transfer size: " . strlen($encrypted) . " bytes");

							$result = $utils->curlPost($url, array('m' => $encrypted));

							$this->log("PEER RESPONDS:");
							$this->log($result);

							if ('<ok/>' == substr($result, -5)) {
								$this->log("PEER CONFIRMS RECIEPT OF MESSAGES.");
								$kapenta->fileDelete($fileName, true);
								$this->log("Deleted local file: $fileName");
								$dataSent = true;
							}

							$sent = true;

						} // end if priority and not sent
					} // end for each file
				} // end for each priority

				if (false == $sent) { $this->log("queue empty"); }

			} // end if not firewalled
		} // end foreach peer

		return $dataSent;

	} // end $this->push()

	//----------------------------------------------------------------------------------------------
	//.	log a status message to the console
	//----------------------------------------------------------------------------------------------

	function log($msg) {
		global $kapenta;
		if (true == $this->chatty) { echo $kapenta->time() . ' ' . $msg . "\n"; }
	}

}

?>
