<?

	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add a new awareNet server to the sync module
//--------------------------------------------------------------------------------------------------
//TODO: make and replace with a stanard 'newserver' generated action

	if ('admin' != $user->role) { $page->do403(); }		// only admins can do this
	
	if ((true == array_key_exists('action', $_POST)) && ('addNewServer' == $_POST['action'])) {

		//------------------------------------------------------------------------------------------
		//	add a new record
		//------------------------------------------------------------------------------------------

		$model = new Sync_Server();
		$model->UID = $kapenta->createUID();			//TODO: remove this?
		$model->servername = $_POST['servername'];
		$model->serverurl = $_POST['serverurl'];
		$model->password = $_POST['password'];
		$model->direction = $_POST['direction'];
		$model->active = $_POST['active'];
		$model->save();

		//------------------------------------------------------------------------------------------
		//	redirect back to list of servers
		//------------------------------------------------------------------------------------------

		$page->do302('sync/listservers/');

	} else { $page->do404(); }

?>
