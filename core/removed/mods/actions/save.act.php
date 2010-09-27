<?

//--------------------------------------------------------------------------------------------------------------
//	action for saving module data
//--------------------------------------------------------------------------------------------------------------

require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------------------
//	save any changes to permissions
//--------------------------------------------------------------------------------------------------------------

	if ((array_key_exists('action', $_POST)) && ($_POST['action'] == 'savePermissions')) {
		$perms = array();
		$m = new KModule($_POST['modulename']);

		foreach($_POST as $var => $val) {
		  if (substr($var, 0, 5) == 'perm:') {
			$permName = trim(substr($var, 5));
			$m->permissions[$permName] = array();

			$lines = explode("\n", $val);
			foreach ($lines as $line) {
			  if (strpos($line, '=') != false) {
				$m->permissions[$permName][] = trim($line);
			  }
			}
		  }
		}

		$m->save();
	}

	authUpdatePermissions();
	$_SESSION['sMessage'] .= "permissions updated..<br/>\n";
	$page->do302('mods/permissions/' . $_POST['modulename']);

?>
