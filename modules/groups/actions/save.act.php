<?

//--------------------------------------------------------------------------------------------------
//	save a group record
//--------------------------------------------------------------------------------------------------

	if (authHas('groups', 'edit', '') == false) { do403(); }
	
	if ( (array_key_exists('action', $_POST))
	   AND ($_POST['action'] == 'saveRecord') 
	   AND (array_key_exists('UID', $_POST))
	   AND (dbRecordExists('groups', sqlMarkup($_POST['UID']))) ) {
	
		require_once($installPath . 'modules/groups/models/group.mod.php');
		
		$p = new Group(sqlMarkup($_POST['UID']));
		
		$p->data['name'] = $_POST['name'];
		$p->data['school'] = $_POST['school'];
		$p->data['description'] = trim($_POST['description']);
		$p->data['type'] = $_POST['type'];
				
		$p->save();
		
		do302('groups/' . $p->data['recordAlias']);
		
	} else { 
		do404();
	}

?>
