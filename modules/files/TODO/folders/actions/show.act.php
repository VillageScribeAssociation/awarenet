<?

	require_once($kapenta->installPath . 'modules/folders/models/folder.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display a folder
//--------------------------------------------------------------------------------------------------
	
	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); }							// check ref
	$UID = $aliases->findRedirect('Files_Folder'); 						// check correct ref

	$model = new folder($req->ref);	
	if (false == $model->loaded) { $page->do404('no such folder'); }
	if (false == $user->authHas('files', 'Files_Folder', 'show', $model->UID)) { $page->do403(); }		

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/folders/actions/show.page.php');
	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['raUID'] = $model->alias;
	$page->blockArgs['userUID'] = $model->createdBy;
	$page->render();

?>
