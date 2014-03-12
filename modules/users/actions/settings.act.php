<?

//--------------------------------------------------------------------------------------------------
//*	toggle user module settings
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	check user defaults
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->registry->get('users.allowpublicsignup')) { 
		$kapenta->registry->set('users.allowpublicsignup', 'no');
	}

	if ('' == $kapenta->registry->get('users.allowteachersignup')) { 
		$kapenta->registry->set('users.allowteachersignup', 'no');
	}

	if ('' == $kapenta->registry->get('users.grades')) { 
		$grades = array(
			'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7',
			'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12', '1. Klasse', '2. Klasse',
			'3. Klasse', '4. Klasse', '5. Klasse', '6. Klasse', '7. Klasse', '8. Klasse',
			'9. Klasse', '10. Klasse', '11. Klasse', '12. Klasse', '13. Klasse', 'Std. 1',
			'Std. 2', 'Std. 3', 'Std. 4', 'Std. 5', 'Std. 6', 'Std. 7', 'Std. 8', 'Std. 9',
			'Std. 10', 'Std. 11', 'Std. 12', 'Alumni', 'Staff'
		);	// add other school systems here

		$gradeStr = implode("\n", $grades);
		$kapenta->registry->set('users.grades', $gradeStr);
	}

	if ('' == $kapenta->registry->get('users.maxmessages')) { $kapenta->registry->set('users.maxmessages', '5'); }

	//----------------------------------------------------------------------------------------------
	//	handle any POST vars
	//----------------------------------------------------------------------------------------------

	foreach($_POST as $key => $value) {
		switch($key) {

			case 'users_allowpublicsignup':	
				$kapenta->registry->set('users.allowpublicsignup', $utils->cleanYesNo($value));	
				break;	//..........................................................................


			case 'users_allowteachersignup':	
				$kapenta->registry->set('users.allowteachersignup', $utils->cleanYesNo($value));	
				break;	//..........................................................................


			case 'users_grades':	
				$kapenta->registry->set('users.grades', trim($value));	
				break;	//..........................................................................

			case 'users_maxmessages':	
				$kapenta->registry->set('users.maxmessages', (int)$value);	
				break;	//..........................................................................

		}
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/users/actions/settings.page.php');
	$kapenta->page->render();

?>
