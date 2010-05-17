<?

	require_once($installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make file info/download box no more than 300px wide
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID [string]
//opt: fileUID - overrides raUID [string]

function files_dnboxnav($args) {
	if (array_key_exists('fileUID', $args)) { $args['raUID'] = $args['fileUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$f = new File($args['raUID']);
	return replaceLabels($f->extArray(), loadBlock('modules/files/views/dnboxnav.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>

