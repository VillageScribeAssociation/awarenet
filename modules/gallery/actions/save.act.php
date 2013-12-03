<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save a Gallery_Gallery object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('action', $_POST)) { $page->do404('action not specified.'); }
	if ('saveRecord' != $_POST['action']) { $page->do404('unsupported action.'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not given.'); }

	$model = new Gallery_Gallery($_POST['UID']);
	if (false == $model->loaded) { $page->do404('Unknown gallery.'); }
	if (false == $user->authHas('gallery', 'gallery_gallery', 'edit', $model->UID)) { 
		$page->do403('You are not authorized to edit this gallery.'); 
	}

	//----------------------------------------------------------------------------------------------
	//	update gallery
	//----------------------------------------------------------------------------------------------

	foreach($_POST as $key => $value) {
		switch($key) {
			case 'title':			$model->title = $utils->cleanTitle($value);			break;
			case 'description':		$model->description = $utils->cleanHtml($value);	break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	notify user and redirect back to gallery page
	//----------------------------------------------------------------------------------------------

	if ('' == $report) {
		$session->msg('Saved changes to gallery: ' . $model->title, 'ok');
	} else {
		$session->msg('Could not save changes to gallery:<br/>' . $report, 'bad');
	}

	$page->do302('gallery/' . $model->alias);			

?>
