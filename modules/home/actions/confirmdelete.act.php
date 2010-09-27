<?

	require_once($kapenta->installPath . 'modules/home/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a static page
//--------------------------------------------------------------------------------------------------
//$req->ref should contain UID of Home_Static object to be deleted

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) {$page->do302('static/list/'); }
	$model = new Home_Static($req->ref);
	if (false == $model->loaded) { $page->do404('Static page not found.'); }
	if (false == $user->authHas('home', 'Home_Static', 'edit', $model->UID))
		{ $page->do403('You are not authorized to delete this page.'); }

	//----------------------------------------------------------------------------------------------
	//	make the confirmation form and show it on the item to be deleted
	//----------------------------------------------------------------------------------------------
	$labels = array('UID' => $model->UID, 'alias' => $model->alias);
	$block = $theme->loadBlock('modules/home/views/confirmdelete.block.php');
	$session->msg($theme->replaceLabels($labels, $block), 'warn');
	$page->do302('home/show/' . $req->ref);
	
?>
