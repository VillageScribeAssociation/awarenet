<?

//--------------------------------------------------------------------------------------------------
//*	toggle user module settings
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	check defaults
	//----------------------------------------------------------------------------------------------
	if ('' == $registry->get('users.allowpublicsignup')) { 
		$registry->set('users.allowpublicsignup', 'no');
	}

	if ('' == $registry->get('users.allowteachersignup')) { 
		$registry->set('users.allowteachersignup', 'no');
	}

	if ('' == $registry->get('users.grades')) { 
		$grades = array(
			'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7',
			'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12', '1. Klasse', '2. Klasse',
			'3. Klasse', '4. Klasse', '5. Klasse', '6. Klasse', '7. Klasse', '8. Klasse',
			'9. Klasse', '10. Klasse', '11. Klasse', '12. Klasse', '13. Klasse', 'Std. 1',
			'Std. 2', 'Std. 3', 'Std. 4', 'Std. 5', 'Std. 6', 'Std. 7', 'Std. 8', 'Std. 9',
			'Std. 10', 'Std. 11', 'Std. 12', 'Alumni', 'Staff'
		);	// add other school systems here

		$gradeStr = implode("\n", $grades);
		$registry->set('users.grades', $gradeStr);
	}

	//----------------------------------------------------------------------------------------------
	//	handle any POST vars
	//----------------------------------------------------------------------------------------------

	foreach($_POST as $key => $value) {
		switch($key) {

			case 'users_allowpublicsignup':	
				$registry->set('users.allowpublicsignup', $utils->cleanYesNo($value));	
				break;	//..........................................................................


			case 'users_allowteachersignup':	
				$registry->set('users.allowteachersignup', $utils->cleanYesNo($value));	
				break;	//..........................................................................


			case 'users_grades':	
				$registry->set('users.grades', trim($value));	
				break;	//..........................................................................

		}
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/users/actions/settings.page.php');
	$page->render();

?>
