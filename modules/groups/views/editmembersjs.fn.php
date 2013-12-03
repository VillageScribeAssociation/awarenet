<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');

//--------------------------------------------------------------------------------------------------
//|	AJAX front end for editing group memebrships
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Groups_Group object [string]
//opt: groupUID - overrides raUID if present [string]

function groups_editmembersjs($args) {
	global $theme;
	$html = '';					//%	return value [string]
	
	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('groupUID', $args)) { $args['raUID'] = $args['groupUID']; }	
	if (false == array_key_exists('raUID', $args)) { return '(group not specified)'; }

	$model = new Groups_Group($args['raUID']);
	if (false == $model->loaded) { return '(unkown group)'; }
	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/groups/views/editmembersjs.block.php');

	$labels = $model->extArray();
	$labels['groupUID'] = $model->UID;

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}


?>
