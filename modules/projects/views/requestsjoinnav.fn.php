<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list requests made by others to join a project
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID or recordAlias of a project [string]
//opt: projectUID - overrides raUID [string]

function projects_requestsjoinnav($args) {
	global $db, $user;
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('projectUID', $args) == true) { $args['raUID'] = $args['projectUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }

	$model = new Projects_Project($args['raUID']);	
	if (false == $model->loaded) { return ''; }

	if ((false == $model->isAdmin($user->UID)) && ('admin' != $user->role)) { return ''; }
	//if (false == $user->authHas('projects', 'Projects_Project', 'administer', $model->UID))
	//	{ return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$pm = $model->getProspectiveMembers();
	if (0 == count($pm)) { return ''; }			// nothing to show

	$html .= "[[:theme::navtitlebox::label=Have Asked To Join:]]\n";
	foreach($pm as $userUID => $role) {
		$addUrl = "%%serverPath%%projects/acceptmember/" . $row['UID'];
		$html .= "[[:users::summarynav::userUID=" . $row['userUID'] . ":]]\n"
			   . "<a href='$addUrl'>[add as project member >> ]</a><hr/>\n";
	}
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
