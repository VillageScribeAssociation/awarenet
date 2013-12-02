<?php

//--------------------------------------------------------------------------------------------------
//*	utility functions to install Kapenta.JS module
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	install the Forums module
//--------------------------------------------------------------------------------------------------
//returns: html report or false if not authorized [string][bool]

function kjs_install_module() {
	global $user;
	global $kapenta;

	$report = "<p>This module does not use the database.</p>";

	$dirs = array(
		"data/kjs/",
		"data/kjs/core/",
		"data/kjs/modules/",
		"data/kjs/theme/"
	);

	foreach($dirs as $dir) {
		if (false == $kapenta->fileIsExtantRW($dir)) {
			$status = "Creating: $dir <br/>\n";
			$kapenta->fileMakeSubdirs($dir . "null.txt", true);
		}
	}

	return $report;
}

//--------------------------------------------------------------------------------------------------
//|	discover if this module is installed
//--------------------------------------------------------------------------------------------------
//:	if installed correctly report will contain HTML comment <!-- installed correctly -->
//returns: HTML installation status report [string]

function kjs_install_status_report() {
	global $kapenta;

	$status = "<!-- installed correctly -->";

	$dirs = array(
		"data/kjs/",
		"data/kjs/core/",
		"data/kjs/modules/",
		"data/kjs/theme/"
	);

	foreach($dirs as $dir) {
		if (false == $kapenta->fs->exists($dir)) { $status .= "Missing: $dir <br/>\n"; }
	}

	return "<p>This module does not use the database.</p>" . $status;
}

?>
