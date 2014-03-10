<?php

	require_once($kapenta->installPath . 'modules/admin/inc/logfile.class.php');

//--------------------------------------------------------------------------------------------------
//*	count most popular pages over the course of a year
//--------------------------------------------------------------------------------------------------
//ref: yy, eg 12 for 2012

	//----------------------------------------------------------------------------------------------
	//	check reference and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if (strlen($kapenta->request->ref) != 2) { $page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	make list of logs from this year
	//----------------------------------------------------------------------------------------------
	$logFiles = $kapenta->fs->search('data/log/', 'pageview.log.php');
	$log = new Admin_Logfile();

	$ofYear = array();

	foreach($logFiles as $logFile) {
		$baseName = basename($logFile);
		if (substr($baseName, 0, 2) == $kapenta->request->ref) {
			$ofYear[] = $logFile;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	extract and collate pageviews
	//----------------------------------------------------------------------------------------------

	$pages = array();
	$pageViews = 0;

	foreach($ofYear as $logFile) {
		echo $logFile . "<br/>\n"; flush();
		$raw = $kapenta->fs->get($logFile);
		$lines = explode("\n", $raw);

		$stub = true;						//%	set to true at end of stub program [bool]
		$buffer = '';						//%	holds current entry [string]


		foreach($lines as $line) {
			if (false == $stub) { $buffer .= trim($line) . "\n"; }

			if ('</entry>' == trim($line)) { 
				$entry = $log->pageviewToArray($buffer);
				$buffer = '';
				$isBot = $log->isBot($entry['useragent']);

				if (false !== strpos($entry['request'], '/minigal/')) {
					$entry['request'] = '/favicon.ico';
				}

				$entry['request'] = str_replace('/pages/default/', '', $entry['request']);

				if ((false == $isBot) && ('/favicon.ico' != $entry['request'])) {
					$reql = strtolower($entry['request']);

					if (false == array_key_exists($reql, $pages)) {
						$pages[$reql] = 1;
					} else {
						$pages[$reql]++;
					}

					$pageViews++;
				}
			}

			if ((true == $stub) && ('?>' == trim($line))) { $stub = false; }
		}

	}

	//----------------------------------------------------------------------------------------------
	//	display pageviews
	//----------------------------------------------------------------------------------------------

	echo "<h2>Non-bot pageviews: " . $pageViews . "</h2>\n";

	arsort($pages);
	foreach($pages as $reql => $count) {
		echo "$count $reql<br/>\n";
	}

?>
