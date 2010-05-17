<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list project members for the nav (300 px wide)
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or projects entry [string]
//opt: editmode - set to yes to show 'remove member' links [string]
//opt: projectUID - overrides raUID [string]

function projects_listmembersnav($args) {
	global $user;
	$editmode = 'no';
	if (authHas('projects', 'view', '') == false) { return false; }
	if (array_key_exists('editmode', $args) == true) { $editmode = $args['editmode']; }
	if (array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }

	$model = new project($args['raUID']);
	$members = $model->getMembers();
	$html = '';

	$isAdmin = false;
	foreach($members as $userUID => $role) { if ($userUID == $user->data['UID']) { $isAdmin = true; } }	

	foreach($members as $userUID => $role) {
		$html .= "[[:users::summarynav::userUID=" . $userUID 
			   . "::extra=(" . $role . ")::target=_parent:]]\n";

		if ( (true == $isAdmin) && ($userUID != $user->data['UID']) && ('yes' == $editmode) ) {
			$rmUrl = "%%serverPath%%projects/editmembers/removemember_" . $userUID . "/" . $args['projectUID'];
			$html .= "<a href='" . $rmUrl . "'>[ remove member &gt;&gt; ]</a><br/>";
		}
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

