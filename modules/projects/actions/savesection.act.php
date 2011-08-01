<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a project section
//--------------------------------------------------------------------------------------------------
//postarg: action - must be 'saveSection' [string]
//postarg: UID - UID of a Projects_Project object [string]
//postarg: sectionUID - UID of project section [string]
//postarg: sectionTitle - name of project section [string]
//postarg: content - html [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST vars
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('action not specified'); }
	if ('saveSection' != $_POST['action']) { $page->do404('action not supported'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not specified'); }

	$model = new Projects_Project($_POST['UID']);
	if (false == $model->loaded) { $page->do404('Project not found.'); }

	if (false == $user->authHas('projects', 'projects_project', 'edit', $model->UID)) {
		$page->do403('You are not authorized to edit this project.'); 
	}

	if (false == array_key_exists('sectionUID', $_POST)) { $page->do404('no section UID'); }
	if (false == array_key_exists('sectionTitle', $_POST)) { $page->do404('no section Title'); }
	if (false == array_key_exists('content', $_POST)) { $page->do404('no content given'); }

	$sectionUID = $_POST['sectionUID'];
	$sectionTitle = $utils->cleanTitle($_POST['sectionTitle']);
	$content = $utils->cleanHtml($_POST['content']);

	if (false == array_key_exists($sectionUID, $model->sections)) { 
		$page->do404('unkown section'); 
	}

	//----------------------------------------------------------------------------------------------
	//	check for changes (revision)
	//----------------------------------------------------------------------------------------------
	$oldVersion = $model->getSimpleHtml();
		
	//----------------------------------------------------------------------------------------------
	//	save the changes
	//----------------------------------------------------------------------------------------------
	$model->sections[$sectionUID]['title'] = strip_tags($sectionTitle);
	$model->sections[$sectionUID]['content'] = $content;
	$model->save();

	//----------------------------------------------------------------------------------------------
	//	save revision (if changed)
	//----------------------------------------------------------------------------------------------
	$newVersion = $model->getSimpleHtml();
	if ($newVersion != $oldVersion) {
		$report = $model->saveRevision();
		if ('' == $report) {
			//--------------------------------------------------------------------------------------
			//	raise 'project_saved' event
			//--------------------------------------------------------------------------------------
			$args = array(
				'UID' => $model->UID,
				'user' => $user->UID,
				'section' => $sectionTitle
			);

			$kapenta->raiseEvent('projects', 'project_saved', $args);
			$session->msg('Saved revision.', 'ok'); 

		} else { $session->msg('Revision not saved.', 'bad'); }
	}

	$page->do302('projects/editsection/section_' . $sectionUID .  '/' . $model->alias);

?>
