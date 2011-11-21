<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for editing the abstract
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or projects entry [string]

function projects_editabstractform($args) {
	global $theme;
	global $user;
	global $utils;

	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Projects_Project($args['raUID']);
	if (false == $user->authHas('projects', 'projects_project', 'edit', $model->UID)) { return ''; }

	if ('open' != $model->status) {
		$UID = $model->UID;
		if ('locked' == $model->status) { $html .= '[[:projects::locked::raUID='. $UID .':]]'; }
		if ('closed' == $model->status) { $html .= '[[:projects::closed::raUID='. $UID .':]]'; }
		return $html;
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/projects/views/editabstractform.block.php');
	$ext = $model->extArray();
	$ext['abstract64'] = $utils->b64wrap($ext['abstract']);
	$html = $theme->replaceLabels($ext, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
