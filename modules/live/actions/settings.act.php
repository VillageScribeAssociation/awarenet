<?

//--------------------------------------------------------------------------------------------------
//*	Live module settings page (add / display file associations)
//--------------------------------------------------------------------------------------------------
//postopt: ext - file extension [string]
//postopt: module - module associated with file extension [string]

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403('Admins only', true); }

	//----------------------------------------------------------------------------------------------
	//	change association if submitted
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('action', $_POST)) {
	
		if ('addFileAssociation' == $_POST['action']) {
			$ext = trim(strtolower($_POST['ext']));
			$module = $_POST['module'];
			$allOk = true;

			if ('.' == substr($ext, 0, 1)) { $ext = substr($ext, 1); }

			if ('' == trim($ext)) {
				$allOk = false;
				$kapenta->session->msg('Invalid file extension', 'bad');
			}
			if (false == $kapenta->moduleExists($module)) {
				$allOk = false;
				$kapenta->page->do404('Unknown module: ' . $module);
			}

			if (true == $allOk) {
				$eventFile = 'modules/' . $module . '/events/file_attach.on.php';
				if (false == $kapenta->fs->exists($eventFile)) {

					$msg = ''
					 . "Module '" . $module . "' does not handle 'file_attach' events, "
					 . "it cannot accept file uploads.";

					$kapenta->session->msg($msg, 'bad');
					$allOk = false;
				}
			}

			if (true == $allOk) {
				$kapenta->registry->set('live.file.' . $ext, $module);
				$kapenta->session->msg("Added file association: $ext => $module.", 'ok');
			}
		}

		if ('removeFileAssociation' == $_POST['action']) {
			$ext = $_POST['ext'];

			if ('' !== $kapenta->registry->get('live.file.' . $ext)) {
				$kapenta->registry->delete('live.file.' . $ext);
				$kapenta->session->msg("Removed association for '$ext' files.");
			}
		}

	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/live/actions/settings.page.php');
	$kapenta->page->render();

?>
