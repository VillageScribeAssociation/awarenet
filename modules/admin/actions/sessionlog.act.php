<?php

	require_once($kapenta->installPath . 'modules/admin/inc/logfile.class.php');

//--------------------------------------------------------------------------------------------------
//*	serialize page log into sessions
//--------------------------------------------------------------------------------------------------
//ref: date as yy-mm-dd

	//----------------------------------------------------------------------------------------------
	//	check reference and user role
	//----------------------------------------------------------------------------------------------

	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	if ('' == $kapenta->request->ref) {
		//TODO:  list dates
	}

	$logFile = 'data/log/' . $kapenta->request->ref  . '-pageview.log.php';

	if (false == $kapenta->fs->exists($logFile)) { $kapenta->page->do404('no such log'); }

	//----------------------------------------------------------------------------------------------
	//	parse into array
	//----------------------------------------------------------------------------------------------
	
	$raw = $kapenta->fs->get($logFile);
	$lines = explode("\n", $raw);
	$sessions = array();

	$continue = true;					//%	loop condition [bool]
	$stub = true;						//%	set to true at end of stub program [bool]
	$buffer = '';						//%	holds current entry [string]
	$pageViews = 0;

	$log = new Admin_Logfile();

	foreach($lines as $line) {
		if (false == $stub) { $buffer .= trim($line) . "\n"; }

		if ('</entry>' == trim($line)) { 
			$entry = $log->pageviewToArray($buffer);
			$buffer = '';
			$isBot = $log->isBot($entry['useragent']);
	
			$rip = $entry['remoteip'];
			$dt = $entry['mysqltime'] . ' - ';

			if ((false == array_key_exists($rip, $sessions)) && (false == $isBot)) {
				$sessions[$rip] = array();
				$sessions[$rip][] = $dt . '[host] ' . $entry['remotehost'] . ' (' . $entry['remoteip'] . ')';
				$sessions[$rip][] = $dt . '[ua] ' . $entry['useragent'];
				if ('' != $entry['referrer']) {
					$sessions[$rip][] = $dt
					 . "[ref] <a href='" . $entry['referrer'] . "'>"
					 . $entry['referrer']
					 . "</a>\n";
				}
			}

			if ((false == $isBot) && ('/favicon.ico' != $entry['request'])) {
				$sessions[$rip][] = $dt . htmlentities($entry['request']);
				$pageViews++;
			}
		}

		if ((true == $stub) && ('?>' == trim($line))) { $stub = false; }
	}
	
	//----------------------------------------------------------------------------------------------
	//	print sessions
	//----------------------------------------------------------------------------------------------

	echo "Uniques: " . count($sessions) . "<br/>\n";
	echo "Pageviews: " . $pageViews . "<br/>\n";

	echo "<small>\n";

	foreach($sessions as $session) {
		foreach($session as $entry) {
			echo $entry . "<br/>\n";
		}
		echo "<hr/>\n";
	}

	echo "</small>\n";

?>
