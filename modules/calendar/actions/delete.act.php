<?

//--------------------------------------------------------------------------------------------------
//	delete a record
//--------------------------------------------------------------------------------------------------

	if (authHas('calendar', 'edit', '') == false) { do403(); }

	if ( (array_key_exists('action', $_POST)) 
	  AND ($_POST['action'] == 'deleteRecord') 
	  AND (array_key_exists('UID', $_POST)) 
	  AND (dbRecordExists('calendar', $_POST['UID'])) ) {
	  
		require_once($installPath . 'modules/calendar/models/calendar.mod.php');
	  
		$c = new calendar();
		$c->load($_POST['UID']);
		
		$_SESSION['sMessage'] .= "Deleted coin: " . $c->data['name'];
		
		$c->delete();
		
		do302('calendar/');
	  
	} else { do404(); }

?>
