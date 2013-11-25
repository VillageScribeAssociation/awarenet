<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//*	play an flv or mp4 video using flowplayer 
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404('Video not specified.'); }

	$model = new Videos_Video($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('Video not found.'); }

	if (('public' == $user->role) && ('public' != $model->category)) { $page->do403(); }

	// temporarily disabled while permissions are in flux  TODO: add this back in when stable
	if (false == $user->authHas('videos', 'videos_video', 'show', $model->UID)) { 
		$page->do403(); 
	}

	//----------------------------------------------------------------------------------------------
	//	bump popularity of this item if viewed by someone other than the creator
	//----------------------------------------------------------------------------------------------
	if (('videos_gallery' == $model->refModel) && ($model->createdBy != $user->UID)) {
		// popularity overall
		$args = array('ladder' => 'videos.all', 'item' => $model->UID);
		$kapenta->raiseEvent('popular', 'popularity_bump', $args);
		
		// popularity within this gallery
		$args = array('ladder' => 'videos.g' . $model->refUID, 'item' => $model->UID);
		$kapenta->raiseEvent('popular', 'popularity_bump', $args);
	}

	//----------------------------------------------------------------------------------------------
	//	block for inline editing if permitted
	//----------------------------------------------------------------------------------------------
	$editAuth = $user->authHas('videos', 'videos_video', 'edit', $model->UID);
	$editBlock = '';

	if (($model->createdBy == $user->UID) || (true == $editAuth)) {

		$thumbIfUrl = ''
		 . '%%serverPath%%/images/uploadsingle'
		 . '/refModule_videos'
		 . '/refModel_videos_video'
		 . '/refUID_' . $model->UID
		 . '/category_thumb/';

		$editBlock = ''
		 . "[[:theme::navtitlebox::label=Edit Video Details::toggle=divEVD::hidden=yes:]]\n"
		 . "<div id='divEVD' style='visibility: hidden; display: none;'>\n"
		 . "[[:videos::editvideoform::raUID=" . $model->UID . "::edittags=yes::return=player:]]\n"
		 . "[[:theme::navtitlebox::label=Change Thumbnail::toggle=divEditVideoThumbnail:]]\n"
		 . "<div id='divEditVideoThumbnail'>"
		 . "<iframe name='videoThumb' class='consoleif' id='ifVideoThumb'\n"
		 . "  src='" . $thumbIfUrl . "'\n"
		 . "  width='100%' height='400px' frameborder='0' ></iframe>\n"
		 . "</div>"
		 . "</div><br/>\n";
	}

	if ('swf' == $model->format) { $page->do302('videos/animate/' . $model->alias); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/videos/actions/play.page.php');
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['title'] = $model->title;
	$kapenta->page->blockArgs['caption'] = $model->caption;
	$kapenta->page->blockArgs['raUID'] = $model->alias;
	$kapenta->page->blockArgs['editBlock'] = $editBlock;
	$kapenta->page->render();

?>
