<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a user video gallery
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST variables
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('saveGallery' != $_POST['action']) { $page->do404('Action not supported.'); } 
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not POSTed.'); }

	$UID = $_POST['UID'];
	$model = new Videos_Gallery($UID);
	if (false == $model->loaded) { $page->do404("Could not load gallery.");}
	if (false == $user->authHas('videos', 'videos_gallery', 'edit', $model->UID))
		{ $page->do403('You are not authorized to edit this gallery.'); }

	//----------------------------------------------------------------------------------------------
	//	update the object
	//----------------------------------------------------------------------------------------------
	//TODO: sanitize description
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'title':		$model->title = $utils->cleanTitle($value); 		break;
			case 'description':	$model->description = $utils->cleanHtml($value);	break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was saved and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) { $session->msg('Saved changes to Gallery', 'ok'); }
	else { $session->msg('Could not save Gallery:<br/>' . $report, 'bad'); }

	if (true == array_key_exists('return', $_POST)) { $page->do302($_POST['return']); }
	else { $page->do302('videos/showgallery/' . $model->alias); }

?>
