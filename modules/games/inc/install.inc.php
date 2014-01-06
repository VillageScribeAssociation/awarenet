<?php

//--------------------------------------------------------------------------------------------------
//*	install scripts for games module
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//	install the games module and all components
//--------------------------------------------------------------------------------------------------
//returns: html report [string]

function games_install_module() {
	global $user;
	global $kapenta;
	global $utils;
	global $kapenta;

	if ('admin' != $user->role) { return false; }	// only admins can do this

	$report = "<h3>Installing Games Module</h3>\n";

	//------------------------------------------------------------------------------------------
	//	list and run game install scripts
	//------------------------------------------------------------------------------------------

	$installScripts = $kapenta->fileSearch('modules/games/inc/', '.install.php');

	foreach($installScripts as $scriptFile) {
		$report .= "Found: " . $scriptFile . "<br/>\n";
		$game = str_replace('.install.php', '', basename($scriptFile));		

		require_once($scriptFile);
		$installFn = "games_install_" . $game;

		if (true == function_exists($installFn)) {
			$report .= "<h2>Game: $game</h2>";
			$report .= call_user_func($installFn);
		} else {
			$report .= "Missing install function: $installFn<br/>";
		}

	}

	//------------------------------------------------------------------------------------------
	//	cminds module has no database presence at now
	//------------------------------------------------------------------------------------------
	$report .= "Games module has no objects serialized to the database.";

	//------------------------------------------------------------------------------------------
	//	done
	//------------------------------------------------------------------------------------------
	return $report;
}

//--------------------------------------------------------------------------------------------------
//	discover if this module is installed
//--------------------------------------------------------------------------------------------------
//returns: HTML installation report [string]
// if installed correctly report will contain HTML comment <!-- installed correctly -->

function games_install_status_report() {
	global $user;
	global $kapenta;
	global $kapenta;
	global $session;

	if ('admin' != $user->role) { return false; }	// only admins can do this
	$installed = true;
	$report = '';

	$report = "";

	//------------------------------------------------------------------------------------------
	//	list and run game install scripts
	//------------------------------------------------------------------------------------------

	$installScripts = $kapenta->fileSearch('modules/games/inc/', '.install.php');

	foreach($installScripts as $scriptFile) {
        if (-1 === strpos($scriptFile, '.svn')) {
		    $game = str_replace('.install.php', '', basename($scriptFile));		
		    require_once($scriptFile);

		    $statusFn = "games_install_" . $game . "_status_report";
		    $status = '';

		    if (true == function_exists($statusFn)) {
			    $status = call_user_func($statusFn);
		    } else {
			    $msg = "Missing install status function: $statusFn in $scriptFile<br/>\n";
			    $session->msgAdmin($msg, 'bad');
			    $installed = false;
		    }

		    if (false == strpos($status, "<!-- installed correctly -->")) { $installed = false; }

		    $report .= "<h2>$game</h2>\n" . $status;
        }
	}

	//------------------------------------------------------------------------------------------
	//	cminds module has no database presence at now
	//------------------------------------------------------------------------------------------
	$report .= "<p><small>Games module has no objects serialized to the database.</small></p>";

	//------------------------------------------------------------------------------------------
	//	done
	//------------------------------------------------------------------------------------------
	if (true == $installed) { $report .= "<!-- installed correctly -->"; }

	return $report;
}

?>
