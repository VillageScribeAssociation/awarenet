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

	if (false == array_key_exists('preset', $_POST)) { $kapenta->page->do404("Preset not specified."); }
	if (false == array_key_exists('to', $_POST)) { $kapenta->page->do404("Missing field 'to'"); }

	$model = new Users_Preset($_POST['preset']);
	if (false == $model->loaded) { $kapenta->page->do404('Preset not found.'); }

	if ('self' == $_POST['to']) {
		$check = $model->applyTo($kapenta->user->UID);
		if (true == $check) { $kapenta->session->msg('Set theme customization: ' . $model->title, 'ok'); }
		else { $kapenta->session->msg('Could not set theme customization.', 'bad'); }
		$kapenta->page->do302('users/myaccount/');
	}

	//----------------------------------------------------------------------------------------------
	//	apply to individual users
	//----------------------------------------------------------------------------------------------

	if ('user' == $_POST['to']) {
		if (false == array_key_exists('userRa', $_POST)) { $kapenta->page->do404('userRa not given'); }
		
		$toUser = new Users_User($_POST['userRa']);
		if (false == $toUser->loaded) { 
			$toUser->loadByName($_POST['userRa']);  		// try load by username if not found
			if (false == $toUser->loaded) { $kapenta->page->do404('Unknown User'); }		// give up
		}

		$check = $model->applyTo($toUser->UID);
		if (true == $check) {
			$msg = ''
			 . "Applied preset '" . $model->title
			 . "' to [[:users::namelink::userUID=" . $toUser->UID . ":]].";
			$kapenta->session->msg($msg, 'ok');
		} else {
			$msg = "Error: could not apply theme preset.";
			$kapenta->session->msg($msg, 'bad');
		}

		$kapenta->page->do302('users/themepresets/');
	}

	//----------------------------------------------------------------------------------------------
	//	apply to a school grade
	//----------------------------------------------------------------------------------------------
	
	if ('grade' == $_POST['to']) {
		if (false == array_key_exists('school', $_POST)) { $kapenta->page->do404('School not given.'); }
		if (false == array_key_exists('grade', $_POST)) { $kapenta->page->do404('Grade not given.'); }

		//------------------------------------------------------------------------------------------
		//	query database
		//------------------------------------------------------------------------------------------
		$conditions = array();
		$conditions[] = "grade='" . $kapenta->db->addMarkup($_POST['grade']) . "'";
		$conditions[] = "school='" . $kapenta->db->addMarkup($_POST['school']) . "'";
		$range = $kapenta->db->loadRange('users_user', '*', $conditions);

		//------------------------------------------------------------------------------------------
		//	apply to all users
		//------------------------------------------------------------------------------------------
		foreach($range as $item) {
			$check = $model->applyTo($item['UID']);
			if (true == $check) {
				$msg = ''
				 . "Applied preset '" . $model->title
				 . "' to [[:users::namelink::userUID=" . $item['UID'] . ":]].";
				$kapenta->session->msg($msg, 'ok');
			} else {
				$msg = "Error: could not apply theme preset.";
				$kapenta->session->msg($msg, 'bad');
			}
		}

		$kapenta->page->do302('users/themepresets/');
	}

	//----------------------------------------------------------------------------------------------
	//	to an entire school
	//----------------------------------------------------------------------------------------------
	
	if ('school' == $_POST['to']) {
		if (false == array_key_exists('school', $_POST)) { $kapenta->page->do404('School not given.'); }

		//------------------------------------------------------------------------------------------
		//	query database
		//------------------------------------------------------------------------------------------
		$conditions = array();
		$conditions[] = "school='" . $kapenta->db->addMarkup($_POST['school']) . "'";
		$range = $kapenta->db->loadRange('users_user', '*', $conditions);

		//------------------------------------------------------------------------------------------
		//	apply to all users
		//------------------------------------------------------------------------------------------
		foreach($range as $item) {
			$check = $model->applyTo($item['UID']);
			if (true == $check) {
				$msg = ''
				 . "Applied preset '" . $model->title
				 . "' to [[:users::namelink::userUID=" . $item['UID'] . ":]].";
				$kapenta->session->msg($msg, 'ok');
			} else {
				$msg = "Error: could not apply theme preset.";
				$kapenta->session->msg($msg, 'bad');
			}
		}

		$kapenta->page->do302('users/themepresets/');
	}

	//----------------------------------------------------------------------------------------------
	//	to an entire site
	//----------------------------------------------------------------------------------------------

	if ('everyone' == $_POST['to']) {
		$check = $model->makeDefault();
		if (true == $check) { $kapenta->session->msg('Replaced default theme.', 'ok'); }
		else { $kapenta->session->msg('Could not replace default theme.', 'bad'); }

		$sql = "select * from users_user";
		$result = $kapenta->db->query($sql);
		while($row = $kapenta->db->fetchAssoc($result)) {
			$item = $kapenta->db->rmArray($row);
			
			$check = $model->applyTo($item['UID']);
			if (true == $check) {
				$msg = ''
				 . "Applied preset '" . $model->title
				 . "' to [[:users::namelink::userUID=" . $item['UID'] . ":]].";
				$kapenta->session->msg($msg, 'ok');
			} else {
				$msg = "Error: could not apply theme preset.";
				$kapenta->session->msg($msg, 'bad');
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	on error...
	//----------------------------------------------------------------------------------------------

	$kapenta->session->msg("Unrecognized: " . $_POST['to'], 'bad');
	$kapenta->page->do302('users/themepresets/');

?>
