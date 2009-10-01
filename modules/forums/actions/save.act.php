<?

//--------------------------------------------------------------------------------------------------
//	save a forums entry
//--------------------------------------------------------------------------------------------------

	if (authHas('forums', 'edit', '') == false) { do403(); }
	require_once($installPath . 'modules/forums/models/forum.mod.php');

	if (array_key_exists('action', $_POST)) {

		//------------------------------------------------------------------------------------------
		//	save from edit form
		//------------------------------------------------------------------------------------------

		if ($_POST['action'] == 'saveRecord') {
			$model = new Forum($_POST['UID']);
			$model->data['title'] = $_POST['title'];
			$model->data['description'] = $_POST['description'];
			$model->save();
			$_SESSION['sMessage'] .= "Saved changes to forums.<br/>\n";
			do302('forums/' . $model->data['recordAlias']);			
		}

		//------------------------------------------------------------------------------------------
		//	add a user to list of moderators/members/bans
		//------------------------------------------------------------------------------------------

		if ($_POST['action'] == 'addForumUser') {
			// TODO			
			$_SESSION['sMessage'] .= "Saved changes to forums.<br/>\n";
			do302('forums/' . $model->data['recordAlias']);			
		}

	}

?>
