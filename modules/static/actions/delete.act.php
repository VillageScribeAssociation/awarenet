<?

//--------------------------------------------------------------------------------------------------------------
//	delete a static page
//--------------------------------------------------------------------------------------------------------------

	if ($user->authHas('home', 'Home_Static', 'edit', 'TODO:UIDHERE') == false) { $kapenta->page->do403(); }
	
	if ( (array_key_exists('action', $_POST)) 
	     AND ($_POST['action'] == 'deleteStaticPage') 
	     AND (array_key_exists('UID', $_POST)) 
	     AND ( $kapenta->db->objectExists('static', $kapenta->db->addMarkup($_POST['UID']))) 
	   ) {
	   
		require_once($kapenta->installPath . 'modules/static/models/static.mod.php');
		$model = new StaticPage($_POST['UID']);
		$model->delete();
	
		$kapenta->page->do302('static/list/');	 
	     
	} else {
		$_SESSION['sMessage'] = '<b>ERROR</b> action or page UID not provided<br/>';
		$kapenta->page->do302('static/list/');
	}

?>