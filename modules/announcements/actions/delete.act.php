<?

//--------------------------------------------------------------------------------------------------
//	delete a record
//--------------------------------------------------------------------------------------------------

	if (authHas('announcements', 'edit', '') == false) { do403(); }

	if ( (array_key_exists('action', $_POST)) 
	  AND ($_POST['action'] == 'deleteRecord') 
	  AND (array_key_exists('UID', $_POST)) 
	  AND (dbRecordExists('announcements', $_POST['UID'])) ) {
	  
		require_once($installPath . 'modules/announcements/models/announcements.mod.php');
	  
		$model = new Announcement();
		$model->load($_POST['UID']);
		
		$_SESSION['sMessage'] .= "Deleted announcement: " . $model->data['title'];
		
		$model->delete();
		
		do302('announcements/');
	  
	} else { do404(); }

?>
