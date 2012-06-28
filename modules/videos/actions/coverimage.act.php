<?php

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//*	action to display a video's cover image
//--------------------------------------------------------------------------------------------------
//+	This is a placeholder action to allow further modification of the image in the future.

	if ('' == $req->ref) { $page->do404('Video not specified.'); }
	$model = new Videos_Video($req->ref);
	if (false == $model->loaded) { $page->do404('Video not found.'); }

	$size = 'widtheditor';
	if (true == array_key_exists('size', $req->args)) { $size = $req->args['size']; }

	$imgUrl = ''
	 . 'images/showdefault'
	 . '/size_' . $size
	 . '/refModule_videos'
	 . '/refModel_videos_video'
	 . '/refUID_' . $model->UID;

	$page->do301($imgUrl);

?>
