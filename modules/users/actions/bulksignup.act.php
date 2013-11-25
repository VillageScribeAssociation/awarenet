<?

//--------------------------------------------------------------------------------------------------
//*	action for adding large numbers of users at once
//--------------------------------------------------------------------------------------------------
//+	user list should be CSV with the format: firstname, surname, username, password

	//----------------------------------------------------------------------------------------------
	//	check permissions 
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	//TODO: permission

	$school = '';			//%	UID fo a schools_school object [string]
	$grade = '';			//%	grade to add students to [string]
	$students = '';			//%	list of students in CSV form, one per line [string]

	//----------------------------------------------------------------------------------------------
	//	handle POST
	//----------------------------------------------------------------------------------------------
	if ((true == array_key_exists('action', $_POST)) && ('batchCreate' == $_POST['action'])) {
		$ok = true;
		if (true == array_key_exists('school', $_POST)) { $school = trim($_POST['school']); }
		if (true == array_key_exists('grade', $_POST)) { $grade = trim($_POST['grade']); }
		if (true == array_key_exists('students', $_POST)) { $students = $_POST['students']; }

		if (('' == $school) || ('' == $grade) || ('' == $students)) {
			$session->msg('Please complete all fields.', 'bad');
			$ok = false;
		}

		if (('' != $school) && (false == $db->objectExists('schools_school', $school))) {
			$session->msg('Unknown school.', 'bad');
			$ok = false;
		}

		if (true == $ok) {
			$lines = explode("\n", $students);
			$goodTable = array(array('Firstname', 'Surname', 'Username', 'Password'));
			$badTable = array(array('Firstname', 'Surname', 'Username', 'Password'));

			foreach($lines as $line) {
				$line = str_replace("\t", ',', $line);
				$parts = explode(',', trim($line));

				$parts[0] = trim($parts[0]);
				$parts[1] = trim($parts[1]);
				$parts[2] = trim($parts[2]);
				$parts[3] = trim($parts[3]);

				//echo "line: $line <br/>\n";
				//foreach($parts as $i => $part) { echo "part $i - $part <br/>\n"; }

				if (4 == count($parts)) {
					$model = new Users_User();
					$model->UID = $kapenta->createUID();
					$model->school = $school;
					$model->grade = $grade;
					$model->role = 'student';
					$model->lang = 'en';

					$model->firstname = $parts[0];
					$model->surname = $parts[1];
					$model->username = $parts[2];
					$model->password = sha1($parts[3] . $model->UID);

					$model->lang = 'en';
					$model->lastOnline = $db->datetime();
					$model->createdOn = $db->datetime();
					$model->createdBy = $user->UID;

					$tableRow = array($parts[0], $parts[1], $parts[2], $parts[3]);

					$report = '';
					$report = $model->save();
					if ('' == $report) {
						//$namelink = "[[:users::namelink::userUID=" . $model->UID . ":]]";
						//$session->msg('Created user account: ' . $namelink, 'ok');
						$goodTable[] = $tableRow;

					} else {
						$name = $parts[0] . ' ' . $parts[1] . ' (' . $parts[2] . ')';
						$msg = 'Could not create user account for ' . $name . '<br/>';
						$session->msg($msg . $report, 'bad');
						$badTable = $tableRow;
					}
				}
			}

			$goodTableHtml = $theme->arrayToHtmlTable($goodTable, true, true);
			$badTableHtml = $theme->arrayToHtmlTable($badTable, true, true);
		
			$msg = '';
			if (count($goodTable) > 1) { $msg .= "<h3>Registered:</h3>$goodTableHtml<br/>"; }
			if (count($badTable) > 1) { $msg .= "<h3>Failed:</h3>$badTableHtml<br/>"; }
			$session->msg($msg, 'info');

		}

	}

	//----------------------------------------------------------------------------------------------
	//	show the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/users/actions/bulksignup.page.php');
	$kapenta->page->blockArgs['blSchool'] = $school;
	$kapenta->page->blockArgs['blGrade'] = $grade;
	$kapenta->page->render();

?>
