<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a Files_File object
//--------------------------------------------------------------------------------------------------
//	needs the file's UID/recordAlias and optionally /return_uploadmultiple
	
	//----------------------------------------------------------------------------------------------
	//	check page arguments and authorisation
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->page->do404('File not specified (UID).'); }

	$model = new Files_File($kapenta->request->ref);

	if (false == $model->loaded) { $kapenta->page->do404('File not found.'); }
	if (false == $user->authHas($model->refModule, $model->refModel, 'files-add', $model->refUID))
		{ $kapenta->page->do403(); }
	
	$return = '';
	if (true == array_key_exists('return', $kapenta->request->args)) { $return = $kapenta->request->args['return']; }
	
	//----------------------------------------------------------------------------------------------
	//	load the page :-)
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/files/actions/edit.if.page.php');
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->blockArgs['return'] = $return;
	$kapenta->page->render();

?>
