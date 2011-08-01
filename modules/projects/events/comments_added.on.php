<?

require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//-------------------------------------------------------------------------------------------------
//|	fired when a comment is added
//-------------------------------------------------------------------------------------------------
//arg: refModule - name of module to which a comment was attached [string]
//arg: refModel - type of object to which comment was attached [string]
//arg: refUID - UID of object to which comment was attached [string]
//arg: commentUID - UID of the new comment [string]
//arg: comment - text/html of comment [string]

function projects__cb_comments_added($args) {
	global $kapenta;
	global $db;
	global $user;
	global $notifications;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------	
	if (false == array_key_exists('refModule', $args)) 		{ return false; }
	if (false == array_key_exists('refUID', $args)) 		{ return false; }
	if (false == array_key_exists('commentUID', $args)) 	{ return false; }
	if (false == array_key_exists('comment', $args)) 		{ return false; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if ('projects' != $refModule) { return false; }
	
	$model = new Projects_Project($refUID);
	if (false == $model->loaded) { return false; }
	$u = new Users_User($model->createdBy);
	if (false == $u->loaded) { return false; }
	
	//----------------------------------------------------------------------------------------------
	//	send notifications to project members
	//----------------------------------------------------------------------------------------------
	$ext = $model->extArray();
	$title = $user->getName() . " commented on project " . $model->title;
	$url = $ext['viewUrl'] . '#comment' . $args['commentUID'];

	$nUID = $notifications->create(
		$refModule, $refModel, $refUID, 'comments_added', $title, $args['comment'], $url
	);

	$notifications->addUser($nUID, $user->UID);
	$notifications->addFriends($nUID, $user->UID);

	//----------------------------------------------------------------------------------------------
	//	add all project members
	//----------------------------------------------------------------------------------------------	
	$ea = array(
		'projectUID' => $model->UID, 
		'notificationUID' => $nUID
	);

	$kapenta->raiseEvent('projects', 'notify_project', $ea);

	//----------------------------------------------------------------------------------------------
	//	add anyone who has commented on this project
	//----------------------------------------------------------------------------------------------	
	$ea = array(
		'refModule' => 'projects',
		'refModel' => 'projects_project',
		'refUID' => $model->UID,
		'notificationUID' => $nUID
	);

	$kapenta->raiseEvent('comments', 'notify_commenters', $ea);

	//----------------------------------------------------------------------------------------------
	//	ok, done
	//----------------------------------------------------------------------------------------------	
	return true;
}

//-------------------------------------------------------------------------------------------------
?>
