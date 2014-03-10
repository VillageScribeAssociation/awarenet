<?

	require_once($kapenta->installPath . 'modules/code/models/bugs.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/code.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/codeproject.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/coderevision.mod.php');

//--------------------------------------------------------------------------------------------------
//	show a record
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID or code entry

function code_show($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$s = new code($args['raUID']);
	return replaceLabels($s->extArray(), loadBlock('modules/code/views/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>