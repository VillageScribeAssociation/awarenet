<?

//--------------------------------------------------------------------------------------------------
//	delete a record
//--------------------------------------------------------------------------------------------------

	if (authHas('gallery', 'edit', '') == false) { do403(); }

	if ( (array_key_exists('action', $_POST)) 
	  AND ($_POST['action'] == 'deleteRecord') 
	  AND (array_key_exists('UID', $_POST)) 
	  AND (dbRecordExists('gallery', $_POST['UID'])) ) {
	  
		require_once($installPath . 'modules/gallery/models/gallery.mod.php');
	  
		$model = new Gallery();
		$model->load($_POST['UID']);
		
		$_SESSION['sMessage'] .= "Deleted gallery: " . $model->data['title'];
		
		$model->delete();
		
		do302('gallery/');
	  
	} else { do404(); }

?>
