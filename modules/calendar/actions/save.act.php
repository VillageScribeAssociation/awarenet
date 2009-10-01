<?

//--------------------------------------------------------------------------------------------------
//	save a calendar entry
//--------------------------------------------------------------------------------------------------

	if (authHas('calendar', 'edit', '') == false) { do403(); }
	
	if ( (array_key_exists('action', $_POST))
	   AND ($_POST['action'] == 'saveCalendar') 
	   AND (array_key_exists('UID', $_POST))
	   AND (dbRecordExists('calendar', sqlMarkup($_POST['UID']))) ) {
	
		require_once($installPath . 'modules/calendar/models/calendar.mod.php');
		
		$c = new Calendar(sqlMarkup($_POST['UID']));
		
		$c->data['title'] = $_POST['title'];
		$c->data['venue'] = $_POST['venue'];
		$c->data['category'] = $_POST['category'];
		$c->data['year'] = $_POST['year'];
		$c->data['month'] = $_POST['month'];
		$c->data['day'] = $_POST['day'];
		$c->data['eventStart'] = $_POST['eventStart'];
		$c->data['eventEnd'] = $_POST['eventEnd'];
		$c->data['content'] = $_POST['content'];
		$c->data['published'] = 'yes';
				
		$c->save();
		
		do302('calendar/' . $c->data['recordAlias']);
		
	} else { 
		echo "UID: " . $_POST['UID'] . " action: " . $_POST['action'] . "<br/>\n";
		
		if (dbRecordExists('calendar', sqlMarkup($_POST['UID']))) {
			echo "record exists";
		} else {
			echo "record does not exist";
		}
	}

?>
