<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	article statistics formatted for nav
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Projects_Project object [string]

function projects_statsnav($args) {
		global $kapenta;
		global $kapenta;
		global $theme;

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check argument and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return ''; }
	
	$model = new Projects_Project($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $kapenta->user->authHas('projects', 'projects_project', 'show', $model->UID)) { return '';}
	$extArray = $model->extArray();

	//----------------------------------------------------------------------------------------------
	//	look up revision stats
	//----------------------------------------------------------------------------------------------
	$conditions = array("projectUID='" . $model->UID . "'");
	$extArray['totalRevisions'] = $kapenta->db->countRange('projects_change', $conditions);

	//$sql = "select * from Projects_Revision where projectUID='" . $extArray['UID'] . "'";

	//----------------------------------------------------------------------------------------------
	//	assemble the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/projects/views/stats.block.php');
	$html = $theme->replaceLabels($extArray, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
