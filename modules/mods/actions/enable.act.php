<?

//--------------------------------------------------------------------------------------------------------------
//	enable a module
//--------------------------------------------------------------------------------------------------------------

	if ((array_key_exists('action', $_POST)) && ($_POST['action'] == 'enableModule')) {
		require_once($installPath . 'modules/mods/models/kmodule.mod.php');		
		$m = new KModule($_POST['modulename']);
		if ($m->installed == 'yes') {
			$m->enabled = 'yes';
			$m->save();
			$_SESSION['sMessage'] .= "Module " . $_POST['modulename'] . " enabled.<br/>\n";
			do302('mods/' . $_POST['modulename']);

		} else {
			do302('mods/' . $_POST['modulename']);
			$_SESSION['sMessage'] .= "Module must be installed before it can be enabled.";
		}
	}

?>
