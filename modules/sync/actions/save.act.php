<?

//--------------------------------------------------------------------------------------------------
//	save updates to server records
//--------------------------------------------------------------------------------------------------

	if ($user->data['ofGroup'] != 'admin') { do403(); } // only admins can use this module
	require_once($installPath . 'modules/sync/models/server.mod.php');

	if ( (array_key_exists('action', $_POST) == true) 
		&& ($_POST['action'] == 'updateServer')
		&& (array_key_exists('UID', $_POST) == true)
		&& (dbRecordExists('servers', $_POST['UID']) == true) ) {

		//------------------------------------------------------------------------------------------
		//	save changes to a server record
		//------------------------------------------------------------------------------------------

		$model = new Server($_POST['UID']);
		$model->data['servername'] = $_POST['servername'];
		$model->data['serverurl'] = $_POST['serverurl'];
		$model->data['password'] = $_POST['password'];
		$model->data['direction'] = $_POST['direction'];
		$model->data['active'] = $_POST['active'];
		$model->save();

		//------------------------------------------------------------------------------------------
		//	redirect back to list of servers
		//------------------------------------------------------------------------------------------
		
		$_SESSION['sMessage'] .= "Server record " . $_POST['UID'] . " updated<br/>\n";
		do302('sync/listservers/');

	}
	
	do404();

?>
