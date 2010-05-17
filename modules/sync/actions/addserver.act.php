<?

//--------------------------------------------------------------------------------------------------
//	add a new awareNet server to the sync module
//--------------------------------------------------------------------------------------------------

	if ($user->data['ofGroup'] != 'admin') { do403(); }		// only admins can do this
	require_once($installPath . 'modules/sync/models/server.mod.php');
	
	if ((array_key_exists('action', $_POST) == true) && ($_POST['action'] == 'addNewServer')) {

		//------------------------------------------------------------------------------------------
		//	add a new record
		//------------------------------------------------------------------------------------------

		$model = new Server();
		$model->data['UID'] = createUID();
		$model->data['servername'] = $_POST['servername'];
		$model->data['serverurl'] = $_POST['serverurl'];
		$model->data['password'] = $_POST['password'];
		$model->data['direction'] = $_POST['direction'];
		$model->data['active'] = $_POST['active'];
		$model->save();

		//------------------------------------------------------------------------------------------
		//	redirect back to list of servers
		//------------------------------------------------------------------------------------------

		do302('sync/listservers/');

	} else { do404(); }

?>
