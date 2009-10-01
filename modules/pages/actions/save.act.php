<?

//--------------------------------------------------------------------------------------------------
//	save submitted block, redirect to /pages/list
//--------------------------------------------------------------------------------------------------
// TODO: check permissions.  very important, check directory traversal, etc

if ((array_key_exists('action', $_POST) AND ($_POST['action'] == 'savePage'))) {

	$fileName = $installPath . 'modules/' . $_POST['module'] . '/' . $_POST['page'];
	if (file_exists($fileName)) {
		$p = new Page();
		$p->load($fileName);

		foreach($p->data as $key => $oldVal) { 
			if (array_key_exists($key, $_POST)) { $p->data[$key] = $_POST[$key]; } 
		}

		$p->save();

	} else { $_SESSION['sMessage'] .= "no such block :-(<br/>\n"; }

	do302('pages/list/');

}

?>
