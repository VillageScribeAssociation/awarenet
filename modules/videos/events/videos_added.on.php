<?

require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//-------------------------------------------------------------------------------------------------
//|	fired when a video is uploaded / added
//-------------------------------------------------------------------------------------------------
//arg: refModule - name of module to which a video was attached [string]
//arg: refModel - type of object to which video was attached [string]
//arg: refUID - UID of object to which video was attached [string]
//arg: videoUID - UID of the new video [string]
//arg: videoTitle - title of new video [string]

function videos__cb_videos_added($args) {
	global $kapenta;
	global $kapenta;
	global $notifications;	

	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (false == array_key_exists('videoUID', $args)) { return false; }
	if (false == array_key_exists('videoTitle', $args)) { return false; }

	if ('videos' != $args['refModule']) { return false; }
	if ('videos_gallery' != $args['refModel']) { return false; }	//TODO: check

	$model = new Videos_Gallery($args['refUID']);
	if (false == $model->loaded) { return false; }	

	//----------------------------------------------------------------------------------------------
	//	update video count
	//----------------------------------------------------------------------------------------------
	
	$model->videocount = $model->countVideos();
	$model->save();

	//----------------------------------------------------------------------------------------------
	//	create notification
	//----------------------------------------------------------------------------------------------
	$ext = $model->extArray();
	$title = $kapenta->user->getName() . ' added a new video to collection: ' . $model->title;
	$url = $ext['viewUrl'];

	$content = "[[:videos::player::videoUID=" . $args['videoUID'] . ":]]<br/>"
			 . "<a href='" . $ext['viewUrl'] . "'>[ view gallery &gt;&gt; ]</a><br/>";

	$nUID = $notifications->create(
		'videos', 
		'videos_gallery', 
		$model->UID, 
		'videos_added', 
		$title, 
		$content, 
		$url
	);

	//----------------------------------------------------------------------------------------------
	//	add project members, admins and user's friends
	//----------------------------------------------------------------------------------------------
	$notifications->addAdmins($nUID);
	$notifications->addFriends($nUID, $kapenta->user->UID);

	return true;
}

//-------------------------------------------------------------------------------------------------
?>
