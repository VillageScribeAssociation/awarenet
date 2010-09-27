<?

require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//-------------------------------------------------------------------------------------------------
//	fired when an image is added 
//-------------------------------------------------------------------------------------------------
//arg: refModule - name of module to which a comment was attached
//arg: refUID - UID of object to which comment was attached
//arg: imageUID - UID of the new comment
//arg: imageTitle - text/html of comment

function gallery__cb_images_added($args) {
	global $db, $user;
	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (false == array_key_exists('imageUID', $args)) { return false; }
	if (false == array_key_exists('imageTitle', $args)) { return false; }

	if ($args['refModule'] != 'gallery') { return false; }

	//----------------------------------------------------------------------------------------------
	//	update image count
	//----------------------------------------------------------------------------------------------
	$model = new Gallery_Gallery($args['refUID']);
	if (false == $model->loaded) { return false; }
	$model->updateImageCount();

	//----------------------------------------------------------------------------------------------
	//	send notification to friends
	//----------------------------------------------------------------------------------------------
	/* TODO: add notification back
	$ext = $model->extArray();
	$link = "<a href='" . $ext['viewUrl'] . "/'>" . $ext['title'].  '</a>';
	$title = $user->getNameLink() . ' added a new image to their gallery: ' . $link;
	$url = $ext['viewUrl'];
	$imgUID = '';

	$content = "<a href='/images/show/" . $args['imageUID'] . "'>[ view image >> ]</a>";

	$title = $user->getNameLink() . ' added a new image to their gallery: ' . $link;

	notifyFriends($args['refUID'], $args['imageUID'], 
				  $user->getName(), $user->getUrl(),
				  $title, $content, $url, $args['imageUID'] );
	*/

	return true;
}

//-------------------------------------------------------------------------------------------------
?>
