<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a Videos_Gallery object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------		
	if (false == array_key_exists('UID', $kapenta->request->args)) { $kapenta->page->do404(); }

	$model = new Videos_Gallery($kapenta->request->args['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Gallery not found.'); }
	if (false == $user->authHas('videos', 'videos_gallery', 'delete', $model->UID))
		{ $kapenta->page->do403('You are not authorized to delete this gallery.'); }	
	
	//----------------------------------------------------------------------------------------------
	//	make the confirmation block
	//----------------------------------------------------------------------------------------------		
	$labels = array('UID' => $model->UID, 'raUID' => $model->alias);
	$block = $theme->loadBlock('modules/videos/views/confirmdelete.block.php');
	$html = $theme->replaceLabels($labels, $block);
	$session->msg($html, 'warn');
	$kapenta->page->do302('videos/' . $model->alias);

?>
