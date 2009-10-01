<?

//--------------------------------------------------------------------------------------------------
//	save a school record
//--------------------------------------------------------------------------------------------------

	if (authHas('schools', 'edit', '') == false) { do403(); }
	
	if ( (array_key_exists('action', $_POST))
	   AND ($_POST['action'] == 'saveRecord') 
	   AND (array_key_exists('UID', $_POST))
	   AND (dbRecordExists('schools', sqlMarkup($_POST['UID']))) ) {
	
		require_once($installPath . 'modules/schools/models/schools.mod.php');
		
		$model = new School(sqlMarkup($_POST['UID']));
		
		$model->data['name'] = $_POST['name'];
		$model->data['description'] = $_POST['description'];
		$model->data['geocode'] = $_POST['geocode'];
		$model->data['country'] = $_POST['country'];
				
		$model->save();
		
		do302('schools/' . $model->data['recordAlias']);
		
	} else { 
		do404();
	}

?>
