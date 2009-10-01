<?

//--------------------------------------------------------------------------------------------------
//	delete a record
//--------------------------------------------------------------------------------------------------

	if (authHas('folder', 'edit', '') == false) { do403(); }

	if ( (array_key_exists('action', $_POST)) 
	  AND ($_POST['action'] == 'deleteRecord') 
	  AND (array_key_exists('UID', $_POST)) 
	  AND (dbRecordExists('folder', $_POST['UID'])) ) {
	  
		require_once($installPath . 'modules/folder/folder.mod.php');
	  
		$model = new folder();
		$model->load($_POST['UID']);
		
		$_SESSION['sMessage'] .= "Deleted folder: " . $model->data['title'];
		
		$model->delete();
		
		do302('folder/');
	  
	} else { do404(); }

?>
