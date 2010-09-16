<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save a forums entry
//--------------------------------------------------------------------------------------------------

	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('saveRecord' != $_POST['action']) { $page->do404('Action not supported.'); }

	//----------------------------------------------------------------------------------------------
	//	save from edit form
	//----------------------------------------------------------------------------------------------

	$model = new Forums_Board($_POST['UID']);
	if (false == $model->loaded) { $page->do404(); }
	if (false == $user->authHas('forums', 'Forums_Board', 'edit', $model->UID))	{ $page->do403(); }

	$model->title = $_POST['title'];
	$model->description = $_POST['description'];
	if (true == $db->objectExists('Schools_School', $_POST['school'])) 
		{ $model->school = $_POST['school']; }

	$report = $model->save();

	if ('' == $report) { $session->msg('Saved changes to forum: ' . $model->name, 'ok'); }
	else { $session->msg('Could not save changes:<br/>' . $report, 'bad'); }
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
