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
	if (true == array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Projects_Project($args['raUID']);	
	if (false == $model->loaded) { return ''; }

	if ((false == $model->isAdmin($user->UID)) && ('admin' != $user->role)) { return ''; }
	//if (false == $user->authHas('projects', 'projects_project', 'administer', $model->UID))
	//	{ return ''; }

	//----------------------------------------------------------------------------------------------
	//	load prospective memebrs from the database
	//----------------------------------------------------------------------------------------------

	$conditions = array();
	$conditions[] = "projectUID='" . $db->addMarkup($model->UID) . "'";
	$conditions[] = "role='asked'";
	
	$range = $db->loadRange('projects_membership', '*', $conditions);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	if (0 == count($range)) { return ''; }			// nothing to show

	$html .= "[[:theme::navtitlebox::label=Have Asked To Join:]]\n";
	foreach($range as $row) {
		$addUrl = "%%serverPath%%projects/acceptmember/" . $row['UID'];
		$html .= "[[:users::summarynav::userUID=" . $row['userUID'] . ":]]\n"
			   . "<a href='$addUrl'>[add as project member >> ]</a><hr/>\n";
	}
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
