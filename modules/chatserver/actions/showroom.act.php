<?

	require_once($kapenta->installPath . 'modules/chatserver/models/room.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display a chatserver room (including memberships, messages, etc)
//--------------------------------------------------------------------------------------------------
//ref: UID of a Chatserver_Room object [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403('Admins only'); }
	if ('' == $kapenta->request->ref) { $page->do404('Room UID not given'); }
	
	$model = new Chatserver_Room($kapenta->request->ref);

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/chatserver/actions/showroom.page.php');
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['title'] = $model->title;
	$kapenta->page->blockArgs['description'] = $model->description;
	$kapenta->page->blockArgs['rm'] = $model->memberships->rm();			//	membership hash
	$kapenta->page->blockArgs['rh'] = $model->rh();						//	room hash
	$kapenta->page->render();

?>
