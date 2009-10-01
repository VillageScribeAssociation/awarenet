<?

//--------------------------------------------------------------------------------------------------------------
//	delete a static page
//--------------------------------------------------------------------------------------------------------------

	if (authHas('static', 'edit', '') == false) { do403(); }
	
	if ( (array_key_exists('action', $_POST)) 
	     AND ($_POST['action'] == 'deleteStaticPage') 
	     AND (array_key_exists('UID', $_POST)) 
	     AND ( dbRecordExists('static', sqlMarkup($_POST['UID']))) 
	   ) {
	   
		require_once($installPath . 'modules/static/models/static.mod.php');
		$model = new StaticPage($_POST['UID']);
		$model->delete();
	
		do302('static/list/');	 
	     
	} else {
		$_SESSION['sMessage'] = '<b>ERROR</b> action or page UID not provided<br/>';
		do302('static/list/');
	}

?>