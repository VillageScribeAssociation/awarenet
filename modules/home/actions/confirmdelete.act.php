<?

	require_once($kapenta->installPath . 'modules/home/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a static page
//--------------------------------------------------------------------------------------------------
//$kapenta->request->ref should contain UID of Home_Static object to be deleted

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) {$kapenta->page->do302('static/list/'); }
	$model = new Home_Static($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404('Static page not found.'); }
	if (false == $user->authHas('home', 'home_static', 'edit', $model->UID))
		{ $kapenta->page->do403('You are not authorized to delete this page.'); }

	//----------------------------------------------------------------------------------------------
	//	make the confirmation form and show it on the item to be deleted
	//----------------------------------------------------------------------------------------------
	$labels = array('UID' => $model->UID, 'alias' => $model->alias);
	$block = $theme->loadBlock('modules/home/views/confirmdelete.block.php');
	$session->msg($theme->replaceLabels($labels, $block), 'warn');
	$kapenta->page->do302('home/show/' . $kapenta->request->ref);
	
?>
