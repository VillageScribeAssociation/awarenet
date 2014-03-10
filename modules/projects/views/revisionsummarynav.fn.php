<?

	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//*	summarize a Project_Revision in the nav (~300px wide)
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Projects_Revision object [string]

function projects_revisionsummarynav($args) {
		global $user;
		global $db;
		global $theme;

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check argument and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $args)) { return ''; }

	$model = new Projects_Revision($args['UID']);
	if (false == $model->loaded) { return ''; }
	//TODO: verify and enable this permission check
	//if (false == $user->authHas('projects', 'projects_revision', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/projects/views/revisionsummarynav.block.php');
	$ext = $model->extArray();
	if ('' == trim($ext['reason'])) { $ext['reason'] = "<i>(no reason given)</i>"; }
	$html = $theme->replaceLabels($ext, $block);

	return $html;
}

?>
