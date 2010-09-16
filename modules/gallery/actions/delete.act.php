<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a Gallery_Gallery object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('deleteRecord' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('Gallery not specified (UID).'); }
    
	$model = new Gallery_Gallery($_POST['UID']);
	if (false == $user->authHas('gallery', 'Gallery_Gallery', 'delete', $model->UID))
		{ $page->do403('You are not authorzed to delete this gallery.'); }

	//----------------------------------------------------------------------------------------------
	//	delete the gallery and redirect
	//----------------------------------------------------------------------------------------------
	$model->delete();
	$session->msg("Deleted gallery: " . $model->title);
	$page->do302('gallery/');

?>
