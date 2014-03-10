<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//*	increment grade of all users
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	$doIt = false;
	$bumpContent = '';

	if ('admin' != $user->role) { $kapenta->page->do403(); }
	if ((true == array_key_exists('action', $_POST)) && ('bumpGrades' == $_POST['action'])) { 
			$doIt = true; 
	}

	if (false == $doIt) {
		//------------------------------------------------------------------------------------------
		//	show form to select which schools should be bumped
		//------------------------------------------------------------------------------------------
		$bumpContent = "[[:schools::bumpgradeform:]]";

	} else {
		//------------------------------------------------------------------------------------------
		//	map of old to new grades
		//------------------------------------------------------------------------------------------
		$bumpMap = array(
			'Grade 1' => 'Grade 2',		'1. Klasse' => '2. Klasse', 	'Std. 1' => 'Std. 2',
			'Grade 2' => 'Grade 3',		'2. Klasse' => '3. Klasse', 	'Std. 2' => 'Std. 3',
			'Grade 3' => 'Grade 4',		'3. Klasse' => '4. Klasse', 	'Std. 3' => 'Std. 4',
			'Grade 4' => 'Grade 5',		'4. Klasse' => '5. Klasse', 	'Std. 4' => 'Std. 5',
			'Grade 5' => 'Grade 6',		'5. Klasse' => '6. Klasse', 	'Std. 5' => 'Std. 6',
			'Grade 6' => 'Grade 7',		'6. Klasse' => '7. Klasse', 	'Std. 6' => 'Std. 7',
			'Grade 7' => 'Grade 8',		'7. Klasse' => '8. Klasse', 	'Std. 7' => 'Std. 8',
			'Grade 8' => 'Grade 9',		'8. Klasse' => '9. Klasse', 	'Std. 8' => 'Std. 9',
			'Grade 9' => 'Grade 10',	'9. Klasse' => '10. Klasse',	'Std. 9' => 'Std. 10',
			'Grade 10' => 'Grade 11',	'10. Klasse' => '11. Klasse', 	'Std. 10' => 'Std. 11',
			'Grade 11' => 'Grade 12',	'11. Klasse' => '12. Klasse', 	'Std. 11' => 'Std. 12',
			'Grade 12' => 'Alumni',		'12. Klasse' => '13. Klasse', 	'Std. 12' => 'Alumni',
			'13. Klasse' => 'Alumni'
		);

		//------------------------------------------------------------------------------------------
		//	do the bumping
		//------------------------------------------------------------------------------------------
		foreach($_POST as $key => $val) {
			if (('on' == $val) && ('cbSchool' == substr($key, 0, 8))) {		
				$val = str_replace('cbSchool', '', $key);
				$model = new Schools_School($val);

				if (true == $model->loaded) {
					$bumpContent .= "<h2>" . $model->name . "</h2>\n";
					$table = array();
					$table[] = array('Student Name', 'Old grade', 'New grade');

					//------------------------------------------------------------------------------
					//	load all students at this school
					//------------------------------------------------------------------------------
					$conditions = array("school='" . $model->UID . "'");
					$range = $kapenta->db->loadRange('users_user', '*', $conditions, 'surname, firstname');

					foreach($range as $item) {
						if (true == array_key_exists($item['grade'], $bumpMap)) {
							$tempUser = new Users_User($item['UID']);
							$newGrade = $bumpMap[$item['grade']];

							$table[] = array(
								$tempUser->getNameLink(),
								$tempUser->grade,
								$newGrade
							);

							$tempUser->grade = $newGrade;
							$tempUser->save();
						}
					}

					$bumpContent .= $theme->arrayToHtmlTable($table, true, true);					
					$model->lastBump = $kapenta->db->datetime();
					$model->save();
				}

			}
		}		
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/schools/actions/bumpgrades.page.php');
	$kapenta->page->blockArgs['bumpContent'] = $bumpContent;
	$kapenta->page->render();

?>
