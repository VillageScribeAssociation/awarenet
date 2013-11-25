<?

	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');

//--------------------------------------------------------------------------------------------------
//*	displays an iframe for editing forum replies
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	//TODO: check user and timeousness...
	if ('' == $kapenta->request->ref) { $page->do404('Reply not specified.', true); }

	$model = new Forums_Reply($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('Unkown reply.', true); }	

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/forums/actions/editreply.if.page.php');
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['reply'] = $model->UID;
	$kapenta->page->render();

?>
