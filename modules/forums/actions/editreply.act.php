<?

	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');

//--------------------------------------------------------------------------------------------------
//*	displays an iframe for editing forum replies
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	//TODO: check user and timeousness...
	if ('' == $req->ref) { $page->do404('Reply not specified.', true); }

	$model = new Forums_Reply($req->ref);
	if (false == $model->loaded) { $page->do404('Unkown reply.', true); }	

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/forums/actions/editreply.if.page.php');
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['reply'] = $model->UID;
	$page->render();

?>
