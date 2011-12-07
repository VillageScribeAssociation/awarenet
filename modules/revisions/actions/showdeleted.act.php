<?
	
	require_once($kapenta->installPath . 'modules/revisions/models/deleted.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show a specific deleted item
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) {$page->do403(); }
	if ('' == $req->ref) { $page->do404(); }

	$model = new Revisions_Deleted($req->ref);
	if (false == $model->loaded) { $page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/revisions/actions/showdeleted.page.php');
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['refModule'] = $model->refModule;
	$page->blockArgs['refModel'] = $model->refModel;
	$page->blockArgs['refUID'] = $model->refUID;
	$page->render();

?>
