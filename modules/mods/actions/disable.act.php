<?

//--------------------------------------------------------------------------------------------------------------
//	enable a module
//--------------------------------------------------------------------------------------------------------------

	if ((array_key_exists('action', $_POST)) && ($_POST['action'] == 'disableModule')) {
		require_once($installPath . 'modules/mods/models/kmodule.mod.php');		
		$m = new KModule($_POST['modulename']);
		$m->enabled = 'no';
		$m->save();
		$_SESSION['sMessage'] .= "Module " . $_POST['modulename'] . " disabled.<br/>\n";
		do302('mods/' . $_POST['modulename']);
	}

?>
