<?

//--------------------------------------------------------------------------------------------------
//	delete a record
//--------------------------------------------------------------------------------------------------

	if (authHas('schools', 'edit', '') == false) { do403(); }

	if ( (array_key_exists('action', $_POST)) 
	  AND ($_POST['action'] == 'deleteRecord') 
	  AND (array_key_exists('UID', $_POST)) 
	  AND (dbRecordExists('schools', $_POST['UID'])) ) {
	  
		require_once($installPath . 'modules/schools/models/schools.mod.php');
	  
		$model = new School();
		$model->load($_POST['UID']);
		
		$_SESSION['sMessage'] .= "Deleted school: " . $model->data['name'];
		
		$model->delete();
		
		do302('schools/');
	  
	} else { do404(); }

?>
