<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');		

//--------------------------------------------------------------------------------------------------------------
//*	enable a module
//--------------------------------------------------------------------------------------------------------------

	if ((array_key_exists('action', $_POST)) && ($_POST['action'] == 'enableModule')) {

		$m = new KModule($_POST['modulename']);
		if ($m->installed == 'yes') {
			$m->enabled = 'yes';
			$m->save();
			$_SESSION['sMessage'] .= "Module " . $_POST['modulename'] . " enabled.<br/>\n";
			$page->do302('mods/' . $_POST['modulename']);

		} else {
			$page->do302('mods/' . $_POST['modulename']);
			$_SESSION['sMessage'] .= "Module must be installed before it can be enabled.";
		}
	}

?>
