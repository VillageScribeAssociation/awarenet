<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list requests made by others to join a project
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID or recordAlias of a project [string]
//opt: projectUID - overrides raUID [string]

function projects_requestsjoinnav($args) {
	global $kapenta;
	global $user;
	global $theme;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Projects_Project($args['raUID']);	
	if (false == $model->loaded) { return ''; }

	if (false == $user->authHas('projects', 'projects_project', 'editmembers', $model->UID)) {
		return ''; 
	}

	//----------------------------------------------------------------------------------------------
	//	get prospective members
	//----------------------------------------------------------------------------------------------
	$prospective = $model->memberships->getProspectiveMembers();
	if (0 == count($prospective)) { return ''; }					// nothing to show

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/projects/views/requestjoinnav.block.php');
	$html .= "[[:theme::navtitlebox::label=Have Asked To Join:]]\n";

	foreach($prospective as $userUID => $role) {
		$labels = array('projectUID' => $model->UID, 'userUID' => $userUID, 'role' => $role);
		$html .= $theme->replaceLabels($labels, $block);
	}

	$html .= "<br/>";

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
