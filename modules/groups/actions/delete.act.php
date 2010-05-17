<?

//--------------------------------------------------------------------------------------------------
//	delete a record
//--------------------------------------------------------------------------------------------------

	if (authHas('groups', 'edit', '') == false) { do403(); }

	if ( (array_key_exists('action', $_POST)) 
	  AND ($_POST['action'] == 'deleteRecord') 
	  AND (array_key_exists('UID', $_POST)) 
	  AND (dbRecordExists('groups', $_POST['UID'])) ) {
	  
		require_once($installPath . 'modules/groups/models/group.mod.php');
	  
		$model = new group();
		$model->load($_POST['UID']);
		
		$_SESSION['sMessage'] .= "Deleted group: " . $model->data['name'];
		
		$model->delete();
		
		do302('groups/');
	  
	} else { do404(); }

?>
