<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a Files_File object
//--------------------------------------------------------------------------------------------------
//	needs the file's UID/recordAlias and optionally /return_uploadmultiple
	
	//----------------------------------------------------------------------------------------------
	//	check page arguments and authorisation
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404('File not specified (UID).'); }

	$model = new Files_File($req->ref);

	if (false == $model->loaded) { $page->do404('File not found.'); }
	if (false == $user->authHas($model->refModule, $model->refModel, 'files-add', $model->refUID))
		{ $page->do403(); }
	
	$return = '';
	if (true == array_key_exists('return', $req->args)) { $return = $req->args['return']; }
	
	//----------------------------------------------------------------------------------------------
	//	load the page :-)
	//----------------------------------------------------------------------------------------------
	$page->load('modules/files/actions/edit.if.page.php');
	$page->blockArgs['raUID'] = $req->ref;
	$page->blockArgs['return'] = $return;
	$page->render();

?>
