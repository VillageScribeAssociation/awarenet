<?

//--------------------------------------------------------------------------------------------------
//	delete a record
//--------------------------------------------------------------------------------------------------

	if (authHas('forums', 'edit', '') == false) { do403(); }

	if ( (array_key_exists('action', $_POST)) 
	  AND ($_POST['action'] == 'deleteRecord') 
	  AND (array_key_exists('UID', $_POST)) 
	  AND (dbRecordExists('forums', $_POST['UID'])) ) {
	  
		require_once($installPath . 'modules/forums/models/forum.mod.php');
	  
		$model = new Forum();
		$model->load($_POST['UID']);
		
		$_SESSION['sMessage'] .= "Deleted forum: " . $model->data['title'];
		
		$model->delete();
		
		do302('forums/');
	  
	} else { do404(); }

?>
