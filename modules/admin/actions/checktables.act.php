<?

//--------------------------------------------------------------------------------------------------
//*	check / repair database tables
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }


	//----------------------------------------------------------------------------------------------
	//	handle any POST action
	//----------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('repair' == $_POST['action'])) {
		if (false == array_key_exists('tableName', $_POST)) { $kapenta->page->do404('Table not given.'); }
		$tableName = $_POST['tableName'];
		if ('' == $tableName) { $kapenta->page->do404('Table name not given.'); }
		if (false == $kapenta->db->tableExists($tableName)) { $kapenta->page->do404('Unknown table.'); }
		$sql = "REPAIR TABLE `" . $tableName . "`";
		$result = $kapenta->db->query($sql);
		
		$table = array();
		$table[] = array('Table', 'Op', 'Msg_type', 'Msg_text');
		while($row = $kapenta->db->fetchAssoc($result)) {
			$table[] = array($row['Table'], $row['Op'], $row['Msg_type'], $row['Msg_text']);
		}
	
		$msg = ''
		 . 'Repairing table: ' . $tableName . '<br/>'
		 . $theme->arrayToHtmlTable($table, true, true);

		$session->msg($msg, 'warn');
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/admin/actions/checktables.page.php');
	$kapenta->page->render();

?>
