<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	full-page display of an file + caption, etc
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of record [string]

function files_showfull($args) {
	global $theme;

	if (array_key_exists('raUID', $args) == false) { return false; }
	$i = new Files_File($args['raUID']);
	if ($i->fileName == '') { return false; }
	
	$labels = $i->extArray();
	$html = $theme->replaceLabels($labels, $theme->loadBlock('modules/files/showfull.block.php'));
	
	
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>