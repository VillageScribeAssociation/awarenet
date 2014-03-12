<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a Videos_Gallery object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('deleteRecord' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('Gallery not specified (UID).'); }
    
	$model = new Videos_Gallery($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Gallery not found'); }
	if (false == $kapenta->user->authHas('videos', 'videos_gallery', 'delete', $model->UID))
		{ $kapenta->page->do403('You are not authorzed to delete this gallery.'); }

	//----------------------------------------------------------------------------------------------
	//	delete the gallery and redirect
	//----------------------------------------------------------------------------------------------
	$model->delete();
	$kapenta->session->msg("Deleted gallery: " . $model->title);
	$kapenta->page->do302('videos/');

?>
