<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/section.mod.php');

//--------------------------------------------------------------------------------------------------
//*	admin page to display all locked sections
//--------------------------------------------------------------------------------------------------
//reqopt: clear - UID of a section to unlock [string]

	//----------------------------------------------------------------------------------------------
	//	check user role and any POST vars
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	if (true == array_key_exists('clear', $req->args)) {
		$model = new Projects_Section($req->args['clear']);
		if (false == $model->loaded) {
			$session->msg('Unkown project section, could not clear lock.', 'bad');
		} else {
			$model->lockedBy = '';
			$model->lockedOn = '';
			$report = $model->save();
			if ('' == $report) {
				$ext = $model->extArray();
				$session->msg('Cleared lock on section: ' . $ext['titleLink'], 'bad');
				$page->do302('projects/showlocks/');
			} else {
				$session->msg('Could not clear lock:<br/>' . $report, 'bad');
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/projects/actions/showlocks.page.php');
	$page->render();

?>
