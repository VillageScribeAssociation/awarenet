<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a Gallery_Gallery object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('deleteRecord' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('Gallery not specified (UID).'); }
    
	$model = new Gallery_Gallery($_POST['UID']);
	if (false == $user->authHas('gallery', 'gallery_gallery', 'delete', $model->UID))
		{ $kapenta->page->do403('You are not authorzed to delete this gallery.'); }

	//----------------------------------------------------------------------------------------------
	//	delete the gallery and redirect
	//----------------------------------------------------------------------------------------------
	$model->delete();
	$session->msg("Deleted gallery: " . $model->title);
	$kapenta->page->do302('gallery/');

?>
