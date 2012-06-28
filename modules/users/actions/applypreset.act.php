<?

	require_once($kapenta->installPath . 'modules/users/models/preset.mod.php');

//--------------------------------------------------------------------------------------------------
//*	apply a theme preset to one or more users
//--------------------------------------------------------------------------------------------------
//postarg: preset - UID or alias of a Users_Preset object [string]
//postarg: to - who should receive the preset (self|user|grade|school) [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('preset', $_POST)) { $page->do404("Preset not specified."); }
	if (false == array_key_exists('to', $_POST)) { $page->do404("Missing field 'to'"); }

	$model = new Users_Preset($_POST['preset']);
	if (false == $model->loaded) { $page->do404('Preset not found.'); }

	if ('self' == $_POST['to']) {
		$check = $model->applyTo($user->UID);
		if (true == $check) { $session->msg('Set theme customization: ' . $model->title, 'ok'); }
		else { $session->msg('Could not set theme customization.', 'bad'); }
		$page->do302('users/myaccount/');
	}

	//----------------------------------------------------------------------------------------------
	//	apply to individual users
	//----------------------------------------------------------------------------------------------

	if ('user' == $_POST['to']) {
		if (false == array_key_exists('userRa', $_POST)) { $page->do404('userRa not given'); }
		
		$toUser = new Users_User($_POST['userRa']);
		if (false == $toUser->loaded) { 
			$toUser->loadByName($_POST['userRa']);  		// try load by username if not found
			if (false == $toUser->loaded) { $page->do404('Unknown User'); }		// give up
		}

		$check = $model->applyTo($toUser->UID);
		if (true == $check) {
			$msg = ''
			 . "Applied preset '" . $model->title
			 . "' to [[:users::namelink::userUID=" . $toUser->UID . ":]].";
			$session->msg($msg, 'ok');
		} else {
			$msg = "Error: could not apply theme preset.";
			$session->msg($msg, 'bad');
		}

		$page->do302('users/themepresets/');
	}

	//----------------------------------------------------------------------------------------------
	//	apply to a school grade
	//----------------------------------------------------------------------------------------------
	
	if ('grade' == $_POST['to']) {
		if (false == array_key_exists('school', $_POST)) { $page->do404('School not given.'); }
		if (false == array_key_exists('grade', $_POST)) { $page->do404('Grade not given.'); }

		//------------------------------------------------------------------------------------------
		//	query database
		//------------------------------------------------------------------------------------------
		$conditions = array();
		$conditions[] = "grade='" . $db->addMarkup($_POST['grade']) . "'";
		$conditions[] = "school='" . $db->addMarkup($_POST['school']) . "'";
		$range = $db->loadRange('users_user', '*', $conditions);

		//------------------------------------------------------------------------------------------
		//	apply to all users
		//------------------------------------------------------------------------------------------
		foreach($range as $item) {
			$check = $model->applyTo($item['UID']);
			if (true == $check) {
				$msg = ''
				 . "Applied preset '" . $model->title
				 . "' to [[:users::namelink::userUID=" . $item['UID'] . ":]].";
				$session->msg($msg, 'ok');
			} else {
				$msg = "Error: could not apply theme preset.";
				$session->msg($msg, 'bad');
			}
		}

		$page->do302('users/themepresets/');
	}

	//----------------------------------------------------------------------------------------------
	//	to an entire school
	//----------------------------------------------------------------------------------------------
	
	if ('school' == $_POST['to']) {
		if (false == array_key_exists('school', $_POST)) { $page->do404('School not given.'); }

		//------------------------------------------------------------------------------------------
		//	query database
		//------------------------------------------------------------------------------------------
		$conditions = array();
		$conditions[] = "school='" . $db->addMarkup($_POST['school']) . "'";
		$range = $db->loadRange('users_user', '*', $conditions);

		//------------------------------------------------------------------------------------------
		//	apply to all users
		//------------------------------------------------------------------------------------------
		foreach($range as $item) {
			$check = $model->applyTo($item['UID']);
			if (true == $check) {
				$msg = ''
				 . "Applied preset '" . $model->title
				 . "' to [[:users::namelink::userUID=" . $item['UID'] . ":]].";
				$session->msg($msg, 'ok');
			} else {
				$msg = "Error: could not apply theme preset.";
				$session->msg($msg, 'bad');
			}
		}

		$page->do302('users/themepresets/');
	}

	//----------------------------------------------------------------------------------------------
	//	to an entire site
	//----------------------------------------------------------------------------------------------

	if ('everyone' == $_POST['to']) {
		$check = $model->makeDefault();
		if (true == $check) { $session->msg('Replaced default theme.', 'ok'); }
		else { $session->msg('Could not replace default theme.', 'bad'); }

		$sql = "select * from users_user";
		$result = $db->query($sql);
		while($row = $db->fetchAssoc($result)) {
			$item = $db->rmArray($row);
			
			$check = $model->applyTo($item['UID']);
			if (true == $check) {
				$msg = ''
				 . "Applied preset '" . $model->title
				 . "' to [[:users::namelink::userUID=" . $item['UID'] . ":]].";
				$session->msg($msg, 'ok');
			} else {
				$msg = "Error: could not apply theme preset.";
				$session->msg($msg, 'bad');
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	on error...
	//----------------------------------------------------------------------------------------------

	$session->msg("Unrecognized: " . $_POST['to'], 'bad');
	$page->do302('users/themepresets/');

?>
