<?php

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//-------------------------------------------------------------------------------------------------
//|	fired when an image is added 
//-------------------------------------------------------------------------------------------------
//arg: refModule - name of module to which a image was attached [string]
//arg: refModel - type of object to which image was attached [string]
//arg: refUID - UID of object to which image was attached [string]
//arg: imageUID - UID of the new image [string]
//arg: imageTitle - title of new image [string]

function projects__cb_images_added($args) {
	global $kapenta;
	global $db;
	global $user;
	global $notifications;
	global $cache;

	//----------------------------------------------------------------------------------------------
	//	check event arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (false == array_key_exists('imageUID', $args)) { return false; }
	if (false == array_key_exists('imageTitle', $args)) { return false; }

	if ('projects' != $args['refModule']) { return false; }
	//if ('projects_project' != $args['refModel']) { return false; }

	$model = new Projects_Project($args['refUID']);
	if (false == $model->loaded) { return false; }	

	//----------------------------------------------------------------------------------------------
	//	automatically tag the image with the name of the project
	//----------------------------------------------------------------------------------------------

	$detail = array(
		'refModule' => 'images',
		'refModel' => 'images_image',
		'refUID' => $args['imageUID'],
		'tagName' => $model->title
	);

	$kapenta->raiseEvent('tags', 'tags_add', $detail);

	//----------------------------------------------------------------------------------------------
	//	clear the cache
	//----------------------------------------------------------------------------------------------

	$cache->clear("projects-summary-" . $model->UID);

	//----------------------------------------------------------------------------------------------
	//	create notification
	//----------------------------------------------------------------------------------------------
	$ext = $model->extArray();
	$link = "<a href='" . $ext['viewUrl'] . "/'>" . $ext['title'].  '</a>';
	$title = $user->getName() . ' added a new image to project: ' . $ext['title'];
	$url = $ext['viewUrl'];
	$imgUID = '';

	$imgBlock = "[[:images::width300::imageUID=" . $args['imageUID'] . "::link=no:]]";
	$content = "<a href='$url'>$imgBlock<br/>[ view image >> ]</a>";

	$nUID = $notifications->create(
		'projects', 
		'projects_project', 
		$model->UID, 
		'images_added', 
		$title, 
		$content, 
		$url
	);

	//----------------------------------------------------------------------------------------------
	//	add project members, admins and user's friends
	//----------------------------------------------------------------------------------------------
	$members = $model->memberships->getMembers();
	foreach($members as $userUID => $role) { $notifications->addUser($nUID, $userUID); }

	$notifications->addAdmins($nUID);
	$notifications->addFriends($nUID, $user->UID);

	return true;
}

//-------------------------------------------------------------------------------------------------
?>
