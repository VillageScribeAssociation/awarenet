<?

//--------------------------------------------------------------------------------------------------
//	callbacks allow modules to interact with being necessarily dependant on one another
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/projects/models/projects.mod.php');

//--------------------------------------------------------------------------------------------------
//	when a comment is posted
//--------------------------------------------------------------------------------------------------

function projects__cb_comments_add($refUID, $commentUID, $comment) {
	global $user;
	if (dbRecordExists('projects', $refUID) == false) { return false; }
	$model = new Project($refUID);

	//----------------------------------------------------------------------------------------------
	//	send notifications to project members
	//----------------------------------------------------------------------------------------------

	$ext = $model->extArray();
	$link = "<a href='" . $ext['viewUrl'] . "#comment" . $commentUID ."'>" . $ext['title'].  '</a>';
	$title = $user->getNameLink() . ' commented on your project: ' . $link;
	$url = $ext['viewUrl'] . '#comment' . $commentUID;
	$imgUID = '';

	$imgUID = imgGetHeaviestUID('projects', $refUID);

	notifyProject($refUID, $commentUID, 
				  $user->getName(), $user->getUrl(),
				  $title, $comment, $url, $imgUID );

	return true;
}

//--------------------------------------------------------------------------------------------------
//	when an image has been created
//--------------------------------------------------------------------------------------------------

function projects__cb_images_add($refUID, $imageUID, $imageTitle) {
	global $user;
	if (dbRecordExists('projects', $refUID) == false) { return false; }
	$model = new Project($refUID);

	//----------------------------------------------------------------------------------------------
	//	send notifications to project members
	//----------------------------------------------------------------------------------------------

	$ext = $model->extArray();
	$link = "<a href='" . $ext['viewUrl'] . "/'>" . $ext['title'].  '</a>';
	$title = $user->getNameLink() . ' added a new image to your project: ' . $link;
	$url = $ext['viewUrl'];
	$imgUID = '';

	$content = "<a href='/images/show/" . $imageUID . "'>[ view image >> ]</a>";

	notifyProject($refUID, $commentUID, 
				  $user->getName(), $user->getUrl(),
				  $title, $content, $url, $imageUID );

	$title = $user->getNameLink() . ' added a new image to their project: ' . $link;

	notifyFriends($refUID, $commentUID, 
				  $user->getName(), $user->getUrl(),
				  $title, $content, $url, $imageUID );

	return true;
}

?>
