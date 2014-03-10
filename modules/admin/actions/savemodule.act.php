<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a module definintion (ie, module.xml.php)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }

	if (false == array_key_exists('module', $_POST)) { $kapenta->page->do404('Module not supplied.'); }
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not given.'); }

	$module = new KModule($_POST['module']);
	if (false == $module->loaded) { $kapenta->page->do404('Could not load module.'); }	

	switch($_POST['action']) { 

		case 'saveModule':
			//--------------------------------------------------------------------------------------
			//	update the definition	// TODO some input sanitization
			//--------------------------------------------------------------------------------------
			foreach($_POST as $key => $value) {
				switch ($key) {
					case 'version':				$module->version = $value;			break;
					case 'revision':			$module->revision = $value;			break;
					case 'description':			$module->description = $value;		break;
				}
			}

			$module->save();
			$session->msg('Updated module definition.');
			break;	//..............................................................................

		case 'saveDefaultPermissions':
			//--------------------------------------------------------------------------------------
			//	save default permission set	//TODO check format, etc
			//--------------------------------------------------------------------------------------
			$defperms = array();
			if (false == array_key_exists('defaultpermissions', $_POST)) { $kapenta->page->do404(); }
			$lines = explode("\n", $_POST['defaultpermissions']);
			foreach($lines as $line) {
				$session->msg('line: ' . $line);
				if (strlen(trim($line)) > 3) { $defperms[] = $line; }
			}

			$module->defaultpermissions = $defperms;
			$module->save();
			$session->msg('Updated module default permission set.');
			break;	//..............................................................................

		default:
			$kapenta->page->do404('Action not recognized');
			break;	//..............................................................................

	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to edit form
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('admin/editmodule/' . $module->modulename);

?>
