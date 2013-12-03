<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show a record
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Groups_Group object [string]
//opt: groupUID - overrrides raUID if present [string]

function groups_show($args) {
	global $theme;
	global $user;

	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('groupUID', $args)) { $args['raUID'] = $args['groupUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Groups_Group($args['raUID']);

	//TODO: permissions check here, perhaps to implement private groups

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/groups/views/show.block.php');
	$labels = $model->extArray();
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
