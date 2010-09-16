<?

require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//-------------------------------------------------------------------------------------------------
//	fired when an image is added 
//-------------------------------------------------------------------------------------------------
//arg: refModule - name of module to which a comment was attached
//arg: refUID - UID of object to which comment was attached
//arg: imageUID - UID of the new comment
//arg: imageTitle - text/html of comment

function projects__cb_images_added($args) {
	global $db, $user;
	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (false == array_key_exists('imageUID', $args)) { return false; }
	if (false == array_key_exists('imageTitle', $args)) { return false; }

	$model = new Projects_Project($args['refUID']);
	if (false == $model->loaded) { return false; }	

	//----------------------------------------------------------------------------------------------
	//	send notifications to project member and friends of uplaoder
	//----------------------------------------------------------------------------------------------
	/*	TODO: re-add notifications
	$ext = $model->extArray();
	$link = "<a href='" . $ext['viewUrl'] . "/'>" . $ext['title'].  '</a>';
	$title = $user->getNameLink() . ' added a new image to your project: ' . $link;
	$url = $ext['viewUrl'];
	$imgUID = '';

	$content = "<a href='/images/show/" . $args['imageUID'] . "'>[ view image >> ]</a>";

	notifyProject($args['refUID'], $args['commentUID'], 
				  $user->getName(), $user->getUrl(),
				  $title, $content, $url, $args['imageUID'] );

	$title = $user->getNameLink() . ' added a new image to their project: ' . $link;

	notifyFriends($args['refUID'], $args['imageUID'], 
				  $user->getName(), $user->getUrl(),
				  $title, $content, $url, $args['imageUID'] );
	*/

	return true;
}

//-------------------------------------------------------------------------------------------------
?>
