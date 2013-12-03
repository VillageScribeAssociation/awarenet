<?php

//--------------------------------------------------------------------------------------------------
//|	method to list files in the export directory
//--------------------------------------------------------------------------------------------------

function admin_listdbexport($args) {
	global $kapenta;
	global $user;
	global $theme;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$files = $kapenta->listFiles('data/export/');

	$table = array();
	$table[] = array('File', '[x]');

	foreach($files as $file) {
		if (('.' != $file) && ('..' != $file)) {
			$table[] = array(
				"<a href='%%serverPath%%data/export/$file'>" . $file . "</a>",
				"<a href='%%serverPath%%admin/delexport/$file'>[delete]</a>"
			);
		}
	}

	$html = $theme->arrayToHtmlTable($table, true, true);
	
	return $html;
}

?>
