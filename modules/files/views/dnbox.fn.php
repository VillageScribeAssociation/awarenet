<?

	require_once($installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make file info/download box
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID [string]
//opt: fileUID - overrides raUID [string]

function files_dnbox($args) {
	if (array_key_exists('fileUID', $args)) { $args['raUID'] = $args['fileUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$f = new File($args['raUID']);
	return replaceLabels($f->extArray(), loadBlock('modules/files/views/dnbox.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>

