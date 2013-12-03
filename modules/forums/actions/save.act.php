<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a Forums_Board object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('saveRecord' != $_POST['action']) { $page->do404('Action not supported.'); }

	$model = new Forums_Board($_POST['UID']);
	if (false == $model->loaded) { $page->do404('Board not found.'); }
	if (false == $user->authHas('forums', 'forums_board', 'edit', $model->UID))
		{ $page->do403('You are not authorised to edit this board.'); }

	//----------------------------------------------------------------------------------------------
	//	save any changes
	//----------------------------------------------------------------------------------------------
	//TODO: sanitize description

	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'title':			$model->title = $utils->cleanTitle($value);				break;
			case 'description':		$model->description = $utils->cleanHtml($value);		break;
			case 'weight':			$model->weight = (int)$value; 							break;

			case 'school':
				if (true == $db->objectExists('schools_school', $value)) { $model->school = $value;}
				break;
		}
	}
	$report = $model->save();

	if ('' == $report) { 
		$session->msg('Saved changes to forum: ' . $model->title, 'ok'); 
	} else { 
		$session->msg('Could not save changes:<br/>' . $report, 'bad'); 
	}
	$page->do302('forums/' . $model->alias);			

	//----------------------------------------------------------------------------------------------
	//	add a user to list of moderators/members/bans
	//----------------------------------------------------------------------------------------------
	/*	TODO: make this a separate action
	if ($_POST['action'] == 'addForumUser') {
		// TODO			
		$_SESSION['sMessage'] .= "Saved changes to forums.<br/>\n";
		$page->do302('forums/' . $model->alias);			
	}
	*/

?>
