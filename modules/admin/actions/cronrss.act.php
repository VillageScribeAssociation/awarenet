<?php

	require_once($kapenta->installPath . 'core/kcron.class.php');

//--------------------------------------------------------------------------------------------------
//*	this should be called every 10 minutes, it is the entry point of global cron
//--------------------------------------------------------------------------------------------------
//TODO: consider adding a bot user to take credit for changes

	//----------------------------------------------------------------------------------------------
	//	begin output
	//----------------------------------------------------------------------------------------------	

	//	flush one output block to prevent wget timeout due to slow output buffering
	//	assuming max packet size is 4096, output buffer may be smaller, and zlib compression
	//	may mess this up 

	for ($i = 0; $i < 32; $i++) { echo str_repeat(' ', 126) . "\r\n"; }

	//	TODO: check behavior under zlib

	flush();		
	if (true == function_exists('ob_flush')) { @ob_flush(); }
	if (true == function_exists('ob_end_flush')) { @ob_end_flush(); }

	//----------------------------------------------------------------------------------------------
	//	run all module cron scrips / outstanding scheduled tasks
	//----------------------------------------------------------------------------------------------

	$cron = new KCron();
	$report = $cron->run();

	//echo "Premature / testing update.";
	//die();

	$fileName = 'data/log/' . date("y-m-d") . "-cron.log.php";
	$kapenta->fs->put($fileName, $report, false, false, 'a+');

	//----------------------------------------------------------------------------------------------
	//	display report if user is administrator
	//----------------------------------------------------------------------------------------------
	if ('admin' == $user->role) { 
		//------------------------------------------------------------------------------------------
		//	admin report
		//------------------------------------------------------------------------------------------
		//$kapenta->page->load('modules/admin/actions/cron.page.php');
		//$kapenta->page->blockArgs['report'] = $report;
		//$kapenta->page->render();

	} else {
		//------------------------------------------------------------------------------------------
		//	basic status report
		//------------------------------------------------------------------------------------------
		echo "
		<b>Confirm cron:</b><br/>
		<table noborder>
			<tr>
				<td>ten minutes</td>
				<td>" . $kapenta->registry->get('cron.tenmins') . "</td>
			</tr>
			<tr>
				<td>hourly</td>
				<td>" . $kapenta->registry->get('cron.hourly') . "</td>
			</tr>
			<tr>
				<td>daily</td>
				<td>" . $kapenta->registry->get('cron.daily') . "</td>
			</tr>
		</table>
		";


	}

    header('Content-type: application/rss+xml');

    $pubDate = date('D, d M Y H:i:s T', time());

    $rss = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n" 
        . '<rss version="2.0">' . "\n"
        . '<channel>' . "\n"
        . '  <title>awarenet cron</title>' . "\n"
        . '  <description>Regular networking and housekeeping.</description>' . "\n"
        . '  <link>' . $kapenta->serverPath . '</link>' . "\n"
        . '  <pubDate>' . $pubDate . '</pubDate>' . "\n"
        . '  <ttl>900</ttl>' . "\n"
        . '  <item>' . "\n"
        . '    <title>awarenet cron ' . date('D, d M Y H:i:s T', time()) . '</title>' . "\n"
        . '    <pubDate>' . $pubDate . '</pubDate>' . "\n"
        . '    <description>' . "\n";

    $rss = $rss 
        . 'ten minutes: ' . $kapenta->registry->get('cron.tenmins') . "\n"
        . 'hourly: ' . $kapenta->registry->get('cron.hourly') . "\n"
        . 'daily: ' . $kapenta->registry->get('cron.daily') . "\n\n";


	//----------------------------------------------------------------------------------------------
	//	start a new p2p worker process and leave it to run
	//----------------------------------------------------------------------------------------------
	include $kapenta->installPath . 'modules/p2p/actions/worker.act.php';

	//----------------------------------------------------------------------------------------------
	//	fin.
	//----------------------------------------------------------------------------------------------

    $rss = ''
        . '    </description>' . "\n"
        . '  </item>' . "\n"
        . '</channel>' . "\n"
        . '</rss>';

    
    echo $rss;


?>
