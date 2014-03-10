<?
	
	require_once($kapenta->installPath . 'modules/revisions/models/deleted.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show a specific deleted item
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) {$kapenta->page->do403(); }
	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }

	$model = new Revisions_Deleted($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/revisions/actions/showdeleted.page.php');
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['refModule'] = $model->refModule;
	$kapenta->page->blockArgs['refModel'] = $model->refModel;
	$kapenta->page->blockArgs['refUID'] = $model->refUID;
	$kapenta->page->render();

?>
