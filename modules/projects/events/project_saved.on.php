<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	fired when a project is saved
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Projects_Project object [string]
//arg: user - UID of Users_User object, user who saved it [string]
//arg: section - section which was edited [string]

function projects__cb_project_saved($args) {
	global $session;
	global $notifications;	
	global $user;
	global $kapenta;
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('user', $args)) { return false; }
	if (false == array_key_exists('section', $args)) { return false; }

	$model = new Projects_Project($args['UID']);
	if (false == $model->loaded) { return false; }

	//----------------------------------------------------------------------------------------------
	//	check if this event was raised recently (within the hour)
	//----------------------------------------------------------------------------------------------
	$recentUID = $notifications->existsRecent(
		'projects', 'projects_project', $model->UID, '*', 'project_saved', (60 * 60)
	);

	//----------------------------------------------------------------------------------------------
	//	content of notification / annotation
	//----------------------------------------------------------------------------------------------
	$content = "" 
		. "[[:users::namelink::userUID=" . $user->UID . ":]] "
		. "edited the '" . $args['section'] . "' section of this project.<br/>"
		. "<small>" . $kapenta->db->datetime() . "</small><br/>";

	if ('title' == $args['section']) { 
		$content = ''
			. "[[:users::namelink::userUID=" . $user->UID . ":]] "
			. "changed the title of this project to '" . $model->title . "'.<br/>"
			. "<small>" . $kapenta->db->datetime() . "</small><br/>";
	}


	if ('' != $recentUID) {
		//------------------------------------------------------------------------------------------	
		//	project was saved by the same user recently, update the notification
		//------------------------------------------------------------------------------------------
		$notifications->annotate($recentUID, $content);	
		$session->msg('annotating existing notification');

	} else {
		//------------------------------------------------------------------------------------------
		//	notify project members and user's friends
		//------------------------------------------------------------------------------------------
		$ext = $model->extArray();
		$title = "Project update: " . $ext['title'];

		$nUID = $notifications->create(
			'projects', 'projects_project', $model->UID, 'project_saved', 
			$title, $content, $ext['viewUrl']
		);

		$notifications->addProject($nUID, $model->UID);
		$notifications->addFriends($nUID, $user->UID);
		$notifications->addAdmins($nUID);

		//------------------------------------------------------------------------------------------
		//	raise a microbog event for this
		//------------------------------------------------------------------------------------------
		$args = array(
			'refModule' => 'projects',
			'refModel' => 'projects_project',
			'refUID' => $model->UID,
			'message' => '#'. $kapenta->websiteName .' project updated - '. $model->title
		);
		$kapenta->raiseEvent('*', 'microblog_event', $args);

	}

}


?>
