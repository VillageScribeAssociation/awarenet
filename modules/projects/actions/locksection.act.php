<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/section.mod.php');

//--------------------------------------------------------------------------------------------------
//*	locks a section for editing to a particular user
//--------------------------------------------------------------------------------------------------
//postarg: action - set to 'lockSection' [string]
//postarg: UID - UID of Projects_Section object to lock [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404("Action not given."); }	
	if ('lockSection' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404("UID not given."); }

	$model = new Projects_Section($_POST['UID']);
	if (false == $model->loaded) { echo "<fail>Uknown section.</fail>"; die(); }

	if (false == $user->authHas('projects', 'projects_section', 'edit', $model->UID)) {
		echo "<fail>Not authorized.</fail>"; die();
	}

	if ($model->lockedBy != '') { echo "<fail>Already Locked.</fail>"; die(); }

	//----------------------------------------------------------------------------------------------
	//	lock the section
	//----------------------------------------------------------------------------------------------

	$model->lockedBy = $user->UID;
	$model->lockedOn = $kapenta->db->datetime();
	$report = $model->save();

	if ('' != $report) { echo "<fail>$report</fail>"; die(); }

	//----------------------------------------------------------------------------------------------
	//	no basic HTML for now
	//----------------------------------------------------------------------------------------------
	return '<ok/>'

?>
