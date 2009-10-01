<?

//--------------------------------------------------------------------------------------------------
//	add a new book (this creates the root page, not a new page on an existing book)
//--------------------------------------------------------------------------------------------------

	if (authHas('users', 'edit', '') == false) { do403(); }

	require_once($installPath . 'modules/users/models/users.mod.php');
	$model = new Users();

	if (array_key_exists('school', $_POST)) { $model->data['school'] = $_POST['school']; }
	if (array_key_exists('grade', $_POST)) { $model->data['grade'] = $_POST['grade']; }
	if (array_key_exists('firstname', $_POST)) { $model->data['firstname'] = $_POST['firstname']; }
	if (array_key_exists('surname', $_POST)) { $model->data['surname'] = $_POST['surname']; }
	if (array_key_exists('username', $_POST)) { $model->data['username'] = $_POST['username']; }
	if (array_key_exists('ofGroup', $_POST)) { $model->data['ofGroup'] = $_POST['ofGroup']; }

	if (array_key_exists('password', $_POST)) { 
			$model->data['password'] = sha1($_POST['password'] . $model->data['UID']); 
	}

	$verify = $model->verify();

	if ($verify == '') {
		$model->save();
		authUpdatePermissions();
		do302('users/list/');
	} else {
		$verify = str_replace("\n", "<br/>\n", $verify);
		$_SESSION['sMessage'] .= "<div class='inlinequote'>$verify</div><br/>\n";
		do302('users/list/');
	}
	


?>
