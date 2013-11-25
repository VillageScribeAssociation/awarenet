<?php

//--------------------------------------------------------------------------------------------------
//*	Kapenta log file interface
//--------------------------------------------------------------------------------------------------

class KLog {
	
	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $logDir;			//_	location to store log files [string]

	//----------------------------------------------------------------------------------------------
	//.	consrtructor
	//----------------------------------------------------------------------------------------------

	function KLog() {
		global $kapenta;
		$this->logDir = $kapenta->installPath . 'data/log/';
	}

	//----------------------------------------------------------------------------------------------
	//.	record current page view
	//----------------------------------------------------------------------------------------------
	//TODO: consider adding some of these local variables as members of $kapemta

	function logPageView() {
		global $db, $page, $user, $session;

		$fileName = 'data/log/' . date("y-m-d") . "-pageview.log.php";
		if (false == $this->fileExists($fileName)) { $this->makeEmptyLog($fileName);	}
	
		$referer = '';
		if (true == array_key_exists('HTTP_REFERER', $_SERVER))
			{ $referer = $_SERVER['HTTP_REFERER']; }

		$performance = ''
		 . 'time=' . (microtime(true) - $this->loadtime)
		 . '|queries=' . $db->count
		 . '|db_time=' . $db->time;

		if (true == function_exists('memory_get_peak_usage')) {
			$peakMemory = memory_get_peak_usage(true);
			$performance .= "|mem=" . $peakMemory . '';
		}

		$remoteHost = $session->get('remotehost');
		if ('' == $remoteHost) {

			if (
				('10.' == substr($_SERVER['REMOTE_ADDR'], 0, 3)) ||
				('192.' == substr($_SERVER['REMOTE_ADDR'], 0, 4))
			) {
				$session->set('remotehost', $_SERVER['REMOTE_ADDR']);			
			} else {
				$session->set('remotehost', gethostbyaddr($_SERVER['REMOTE_ADDR']));
			}
		}

		$userAgent = '';
		if (true == array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
			$userAgentt = $_SERVER['HTTP_USER_AGENT'];
		}

		$entry = "<entry>\n"
			. "\t<timestamp>" . $this->time() . "</timestamp>\n"
			. "\t<mysqltime>" . $this->datetime() . "</mysqltime>\n"
			. "\t<user>" . $user->username . "</user>\n"
			. "\t<remotehost>" . $remoteHost . "</remotehost>\n"
			. "\t<remoteip>" . $_SERVER['REMOTE_ADDR'] . "</remoteip>\n"
			. "\t<request>" . $_SERVER['REQUEST_URI'] . "</request>\n"
			. "\t<referrer>" . $referer . "</referrer>\n"
			. "\t<useragent>" . $userAgent . "</useragent>\n"
			. "\t<performace>$performance</performance>\n"
			. "\t<uid>" . $page->UID . "</uid>\n"
			. "</entry>\n";

		$result = $this->filePutContents($fileName, $entry, true, false, 'a+');

		if ((microtime(true) - $this->loadtime)	> 5) {
			$msg = 'request=' . $_SERVER['REQUEST_URI'] . '|' . $performance;
			$this->logEvent('page-slow', 'system', 'pageview', $msg);
		}

		//notifyChannel('admin-syspagelog', 'add', base64_encode($entry));
		//$entry = $kapenta->datetime() . " - " . $user->username . ' - ' . $_SERVER['REQUEST_URI'];
		//notifyChannel('admin-syspagelogsimple', 'add', base64_encode($entry));

		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	create an empty log file TODO: use filePutContents
	//----------------------------------------------------------------------------------------------
	//arg: fileName - relative to installPath [string]

	function makeEmptyLog($fileName) {
		//TODO: fix this to work without setup.inc.php
		$defaultLog = "<?\n" 
					. "\tinclude '../../index.php';\n"
					. "\tlogErr('log', 'eventLog', 'direct access by browser');\n"
					. "\tdo404();\n"
					. "?>\n\n";

		$result = $this->filePutContents($fileName, $defaultLog, true, false, 'w+');
		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	record a system event
	//----------------------------------------------------------------------------------------------
	//arg: log - log name [string]
	//arg: subsystem - subsystem name/label [string]
	//arg: fn - function name [string]
	//arg: msg - message to log [string]
	//returns: true on success or false on failure [bool]

	function logEvent($log, $subsystem, $fn, $msg) {
		global $user, $db, $session;

		//------------------------------------------------------------------------------------------
		//	create new log files as necessary and try get user's IP address
		//------------------------------------------------------------------------------------------
		$fileName = 'data/log/' . date("y-m-d") . '-' . $log . '.log.php';
		if (false == $this->fileExists($fileName)) { $this->makeEmptyLog($fileName); }
	
		$remoteAddr = '';
		if (true == array_key_exists('REMOTE_ADDR', $_SERVER))
			{ $remoteAddr = $_SERVER['REMOTE_ADDR']; }

		//------------------------------------------------------------------------------------------
		//	add a new entry to the log file
		//------------------------------------------------------------------------------------------
		$sessUID = isset($session) ? $session->UID : 'undefined' ;
		$userUID = isset($user) ? $user->UID : 'public';

		$entry = "<event>\n";
		$entry .= "\t<datetime>" . $this->datetime() . "</datetime>\n";
		$entry .= "\t<session>" . $sessUID . "</session>\n";
		$entry .= "\t<ip>" . $remoteAddr . "</ip>\n";
		$entry .= "\t<system>" . $subsystem . "</system>\n";
		$entry .= "\t<user>" . $userUID . "</user>\n";
		$entry .= "\t<function>" . $fn . "</function>\n";
		$entry .= "\t<msg>$msg</msg>\n";
		$entry .= "</event>\n";

		$result = $this->filePutContents($fileName, $entry, true, false, 'a+');
		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	record an ordinary system event
	//----------------------------------------------------------------------------------------------
	//arg: granularity - level of detail (0-3) [int]
	//arg: subsystem - subsystem name/label [string]
	//arg: fn - function name [string]
	//arg: msg - message to log [string]
	//returns: true on success or false on failure [bool]

	function logEv($granularity, $subsystem, $fn, $msg) {
		if ($this->logLevel < $granularity) { return false; }
		return $this->logEvent('event', $subsystem, $fn, $msg); 
	}

	//----------------------------------------------------------------------------------------------
	//.	record an error
	//----------------------------------------------------------------------------------------------
	//arg: subsystem - subsystem name/label [string]
	//arg: fn - function name [string]
	//arg: msg - message to log [string]
	//returns: true on success or false on failure [bool]

	function logErr($subsystem, $fn, $msg) { 
		return $this->logEvent('error', $subsystem, $fn, $msg); 
	}

	//----------------------------------------------------------------------------------------------
	//.	log sync activity
	//----------------------------------------------------------------------------------------------
	//DEPRECATED: remove when protocol changeover is complete
	//arg: msg - message to log [string]
	//: this is overused due to development, needs to be trimmed out of a lot of places now
	//: that the sync module is pretty stable.

	function logSync($msg) {
		global $db;
		$fileName = 'data/log/' . date("y-m-d") . '-sync.log.php';
		if (false == $this->fileExists($fileName)) { $this->makeEmptyLog($fileName);	}
		$msg = $this->datetime() . " **************************************************\n". $msg;
		$result = $this->filePutContents($fileName, $msg, true, false, 'a+');
		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	log p2p activity
	//----------------------------------------------------------------------------------------------
	//arg: msg - message to log [string]
	//: this is overused due to development, needs to be trimmed out of a lot of places now
	//: that the sync module is pretty stable.

	function logP2P($msg) {
		global $db;
		$fileName = 'data/log/' . date("y-m-d") . '-p2p.log.php';
		if (false == $this->fileExists($fileName)) { $this->makeEmptyLog($fileName);	}
		$msg = $this->datetime() . " **************************************************\n". $msg;
		$result = $this->filePutContents($fileName, $msg, true, false, 'a+');
		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	log p2p activity
	//----------------------------------------------------------------------------------------------
	//arg: msg - message to log [string]
	//: this is overused due to development, needs to be trimmed out of a lot of places now
	//: that the sync module is pretty stable.

	function logCron($msg) {
		global $db;
		$fileName = 'data/log/' . date("y-m-d") . '-cron.log.php';
		if (false == $this->fileExists($fileName)) { $this->makeEmptyLog($fileName);	}
		$msg = ''
		 . "<event>\n"
		 . "\t<time>" . $this->datetime() . "</time>\n"
		 . "\t<msg>" . htmlentities($msg, ENT_QUOTES, "UTF-8") . "</msg>\n"
		 . "</event>\n";
		$result = $this->filePutContents($fileName, $msg, true, false, 'a+');
		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	log live activity
	//----------------------------------------------------------------------------------------------
	//arg: msg - message to log [string]
	//: this is overused due to development, needs to be trimmed out of a lot of places now
	//: that the sync module is pretty stable.

	function live($msg) {
		echo "DEPRECATED: $kapenta->log->live<br/>\n";
		return false;
	}

}

?>
