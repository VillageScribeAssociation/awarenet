<?php

	require_once($kapenta->installPath  . 'modules/games/inc/register.class.php');

//--------------------------------------------------------------------------------------------------
//*	install scripts for cMinds module
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//	install the cMinds module
//--------------------------------------------------------------------------------------------------
//returns: html report [string]

function games_install_cminds() {
	global $user;
	global $kapenta;
	global $utils;
	global $kapenta;

	if ('admin' != $user->role) { return false; }	// only admins can do this

	$report = "<h3>Installing cMinds Module</h3>\n";

	//------------------------------------------------------------------------------------------
	//	download Main.swf from cminds.org if not present
	//------------------------------------------------------------------------------------------
	//TODO: implement a hash value for versioning

	$fileName = 'modules/games/assets/cminds/Main.swf';
	$sourceUrl = 'http://cminds.org/pilot/production_release/bin-release/Main.swf';

	if (false == $kapenta->fs->exists($fileName)) {
		//------------------------------------------------------------------------------------------
		//	get latest .swf file from cMinds.org
		//------------------------------------------------------------------------------------------
		$report .= "Downloading... $sourceUrl<br/>";

		$kapenta->fileMakeSubdirs($fileName);
		$bin = $utils->curlGet($sourceUrl);

		$report .= "Downloaded " . strlen($bin) . " bytes.<br/>";

		$check = $kapenta->fs->put($fileName, $bin);
		if (false == $check) { $report .= "WARNING: could not save Main.swf.<br/>\n"; }
		
		$hash = $kapenta->fileSha1($fileName);
		$kapenta->registry->set('cminds.main.hash', $hash);

	} else {
		$report .= "All assets present.";
	}

	//------------------------------------------------------------------------------------------
	//	check registration
	//------------------------------------------------------------------------------------------
	$register = new Games_Register();
	if (false == $register->has('cminds')) {
		$register->add('cminds');
		$report .= "<p>Registering on this awarenet instance...</p>\n";
	} else {
		$report .= "<p>cMinds game is registered on this awarenet instance.</p>\n";
	}

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

function games_install_cminds_status_report() {
	global $user;
	global $kapenta;
	global $kapenta;

	if ('admin' != $user->role) { return false; }	// only admins can do this
	$installed = true;
	$report = '';

	//----------------------------------------------------------------------------------------------
	//	check game registration
	//----------------------------------------------------------------------------------------------

	$register = new Games_Register();
	if (false == $register->has('cminds')) {
		$report .= "cMinds is not registered, it will not be available in menus.<br/>";
		$installed = false;
	}

	//----------------------------------------------------------------------------------------------
	//	check game assets
	//----------------------------------------------------------------------------------------------

	if (false == $kapenta->fs->exists('modules/cminds/assets/Main.swf')) {
		//TODO: implement a hash value for versioning
		$report .= "cMinds game files have not yet been downloaded.";
		$installed = false;
	} else {
		$report = "<p>cMinds module requires no further configuration.</p>";
	}

	if (true == $installed) { $report .= "<!-- installed correctly -->"; }

	return $report;
}

?>
