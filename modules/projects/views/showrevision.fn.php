<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show the state of a project when a given revision was saved
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Projects_Revision object [string]
//opt: revisionUID - overrides UID if present [string]

function projects_showrevision($args) {
		global $kapenta;
		global $user;
		global $theme;

	$html = '';		//%	return value [string]


	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('revisionUID', $args)) { $args['UID'] = $args['projectUID']; }
	if (false == array_key_exists('UID', $args)) { return ''; }

	$model = new Projects_Revision($args['UID']);
	if (false == $model->loaded) { return ''; }
	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/projects/views/show.block.php');

	$labels = $model->extArray();

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
