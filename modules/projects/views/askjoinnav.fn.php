<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	ask to join a project
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID or recordAlias of a project [string]
//opt: projectUID - overrides raUID [string]

function projects_askjoinnav($args) {
	global $db, $theme, $user;
	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------	
	if (array_key_exists('projectUID', $args) == true) { $args['raUID'] = $args['projectUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }

	$model = new Projects_Project($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('projects', 'projects_project', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	determine if user is a member of project already
	//----------------------------------------------------------------------------------------------
	$member = false;					//%	is the user already a memebr of this project? [bool]
	$asked = false;						//%	has the user already asked to join this project? [bool]

	if (true == array_key_exists($user->UID, $model->getMembers())) { $member = true; }
	if (true == array_key_exists($user->UID, $model->getProspectiveMembers())) { $asked = true; }
	
	if (true == $member) { return ''; }	//	can't ask to join if you are already a member

	if ((true == $asked) || (true == $member)) {
		if (true == 'asked') {
			$html = "[[:theme::navtitlebox::label=Ask to Join Project:]]\n"
				  . "You have asked to join this project.<br/><br/>";
		}

	} else {
		$labels = array(	'userUID' => $user->UID, 
							'userName' => $user->getName(),
							'projectUID' => $model->UID
						);

		$block = $theme->loadBlock('modules/projects/views/addme.block.php');
		$html = $theme->replaceLabels($labels, $block);
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
