<?php

//--------------------------------------------------------------------------------------------------
//*	installer stub functions
//--------------------------------------------------------------------------------------------------
//+	These are placeholders for actual install methods which will probably become necessary
//+	as this app is split into smaller, optional packages.

//--------------------------------------------------------------------------------------------------
//|	install the Sketchpad module
//--------------------------------------------------------------------------------------------------
//returns: html report or false if not authorized [string][bool]

function sketchpad_install_module() {
		global $kapenta;
		global $user;


	$report = "No dynamic components to install for this version.";

	return $report;
}

//--------------------------------------------------------------------------------------------------
//|	discover if this module is installed
//--------------------------------------------------------------------------------------------------
//:	if installed correctly report will contain HTML comment <!-- installed correctly -->
//returns: HTML installation status report [string]

function sketchpad_install_status_report() {
	global $user;
	if ('admin' != $user->role) { return false; }

	$report = ' <!-- module installed correctly --> ';

	return $report;

}

?>
