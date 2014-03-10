<?php

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//*	action to display a video's cover image
//--------------------------------------------------------------------------------------------------
//+	This is a placeholder action to allow further modification of the image in the future.

	if ('' == $kapenta->request->ref) { $kapenta->page->do404('Video not specified.'); }
	$model = new Videos_Video($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404('Video not found.'); }

	$size = 'widtheditor';
	if (true == array_key_exists('size', $kapenta->request->args)) { $size = $kapenta->request->args['size']; }

	$imgUrl = ''
	 . 'images/showdefault'
	 . '/size_' . $size
	 . '/refModule_videos'
	 . '/refModel_videos_video'
	 . '/refUID_' . $model->UID;

	$kapenta->page->do301($imgUrl);

?>
