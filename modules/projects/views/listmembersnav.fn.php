<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list project members for the nav (300 px wide)
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or projects entry [string]
//opt: editmode - set to yes to show 'remove member' links [string]
//opt: projectUID - overrides raUID [string]

function projects_listmembersnav($args) {
	global $user;
	$editmode = 'no';
	$isAdmin = false;
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }
	if (true == array_key_exists('editmode', $args)) { $editmode = $args['editmode']; }
	if (true == array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (false == array_key_exists('raUID', $args)) { return false; }

	$model = new Projects_Project($args['raUID']);

	if (false == $user->authHas('projects', 'projects_project', 'show', $model->UID)) { 
		return ''; 
	}

	if (true == $user->authHas('projects', 'projects_project', 'administer', $model->UID)) { 
		$isAdmin = true; 
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$members = $model->getMembers();

	foreach($members as $userUID => $urole) {
		$html .= "[[:users::summarynav::userUID=" . $userUID 
			   . "::extra=(" . $urole . ")::target=_parent:]]\n";

		if ( (true == $isAdmin) && ($userUID != $user->UID) && ('yes' == $editmode) ) {
			$rmUrl = "%%serverPath%%projects/editmembers/removemember_". $userUID ."/". $model->UID;
			$html .= "<a href='" . $rmUrl . "'>[ remove member &gt;&gt; ]</a><br/>";
		}
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

