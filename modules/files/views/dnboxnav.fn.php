<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make file info/download box no more than 300px wide
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID [string]
//opt: fileUID - overrides raUID [string]

function files_dnboxnav($args) {
	global $theme;

	if (array_key_exists('fileUID', $args)) { $args['raUID'] = $args['fileUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$f = new Files_File($args['raUID']);
	return $theme->replaceLabels($f->extArray(), $theme->loadBlock('modules/files/views/dnboxnav.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>