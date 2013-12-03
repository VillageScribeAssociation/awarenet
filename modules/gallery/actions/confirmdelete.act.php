<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a Gallery_Gallery object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------		
	if (false == array_key_exists('UID', $kapenta->request->args)) { $page->do404(); }

	$model = new Gallery_Gallery($kapenta->request->args['UID']);
	if (false == $model->loaded) { $page->do404('Gallery not found.'); }
	if (false == $user->authHas('gallery', 'gallery_gallery', 'delete', $model->UID))
		{ $page->do403('You are not authorized to delete this gallery.'); }	
	
	//----------------------------------------------------------------------------------------------
	//	make the confirmation block
	//----------------------------------------------------------------------------------------------		
	$labels = array('UID' => $model->UID, 'raUID' => $model->alias);
	$block = $theme->loadBlock('modules/gallery/views/confirmdelete.block.php');
	$html = $theme->replaceLabels($labels, $block);
	$session->msg($html, 'warn');
	$page->do302('gallery/' . $model->alias);

?>
