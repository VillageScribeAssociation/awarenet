<?

require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//-------------------------------------------------------------------------------------------------
//|	fired when an image is added 
//-------------------------------------------------------------------------------------------------
//arg: refModule - name of module to which a comment was attached
//arg: refUID - UID of object to which comment was attached
//arg: imageUID - UID of the new comment
//arg: imageTitle - text/html of comment

function gallery__cb_images_added($args) {
	global $kapenta;
	global $kapenta;
	global $user;
	global $notifications;
	global $theme;

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
	//	tag with gallery name
	//----------------------------------------------------------------------------------------------

	$eventArgs = array(
		'refModule' => 'images',
		'refModel' => 'images_image',
		'refUID' => $args['imageUID']
	);

	$eventArgs['tagName'] = $model->title;
	$kapenta->raiseEvent('tags', 'tags_add', $eventArgs);

	//----------------------------------------------------------------------------------------------
	//	discover if notification has been sent by this object today
	//----------------------------------------------------------------------------------------------
	$block = ''
	 . '[[:notifications::notifiedtoday' 
	 . '::refModule=gallery'
	 . '::refModel=gallery_gallery'
	 . '::refUID=' . $args['refUID']
	 . '::refEvent=images_added'
	 . ':]]';

	$recentUID = $theme->expandBlocks($block, '');

	//----------------------------------------------------------------------------------------------
	//	create notification and send to friends
	//----------------------------------------------------------------------------------------------
	if ('' == $recentUID) {
		$ext = $model->extArray();
		$url = $ext['viewUrl'];
		$link = "<a href='" . $url . "'>" . $model->title . "</a>";
		$title = $user->getName() . ' added a new image.';
		$refUID = $model->UID;
		$content = "<a href='%%serverPath%%gallery/image/" . $args['imageUID'] . "'>"
			 . "[[:images::show"
			 . "::size=widthindent"
			 . "::raUID=" . $args['imageUID']
			 . "::display=inline"
			 . "::link=no:]]"
			 . "</a>\n<!-- more images -->"
			 . "<br/><a href='" . $url . "'>[ view gallery &gt;&gt; ]</a>";

		$nUID = $notifications->create(
			'gallery', 'gallery_gallery', $refUID, 'images_added', $title, $content, $url
		);

		$notifications->addUser($nUID, $user->UID);
		$notifications->addFriends($nUID, $user->UID);
		//$notifications->addFriends($nUID, $user->UID);
		$notifications->addSchoolGrade($nUID, $user->school, $user->grade);
	}

	//----------------------------------------------------------------------------------------------
	//	update existing notification
	//----------------------------------------------------------------------------------------------
	if ('' != $recentUID) {
		$content = $notifications->getContent($recentUID);
		$content = str_replace('width300', 'width100', $content);

		$newImg = "<a href='/gallery/image/" . $args['imageUID'] . "'>"
			. "[[:images::show"
			 . "::size=width100"
			 . "::display=inline"
			 . "::raUID=" . $args['imageUID']
			 . "::link=no:]]</a>\n";

		// temp fix
		if (false == strpos($content, '<!-- more images -->')) {
			$content = str_replace(":]]</a>", ":]]</a><!-- more images -->", $content);
		}

		$content = str_replace('<!-- more images -->', $newImg . '<!-- more images -->', $content);

		$ext = $model->extArray();
		$title = $ext['userName'] .' added new images to their gallery '. $ext['title'] .'.';

		$notifications->setContent($recentUID, $content);
		$notifications->setTitle($recentUID, $title);
	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return true;
}

//-------------------------------------------------------------------------------------------------
?>
