<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	ask to join a project
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID or recordAlias of a project [string]
//opt: projectUID - overrides raUID [string]

function projects_askjoinnav($args) {
		global $db;
		global $theme;
		global $user;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaslogin:]]'; }
	if (true == array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (false == array_key_exists('raUID', $args)) { return false; }

	$model = new Projects_Project($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('projects', 'projects_project', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	determine if user is a member of project already
	//----------------------------------------------------------------------------------------------
	
	//	can't ask to join if you are already a member
	if (true == $model->memberships->hasMember($user->UID)) { return ''; }

	if (true == $model->memberships->hasAsked($user->UID)) {
		$html = "[[:theme::navtitlebox::label=Ask to Join Project:]]\n"
				  . "You have asked to join this project.<br/><br/>";

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
