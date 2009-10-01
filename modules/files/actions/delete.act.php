<?

//--------------------------------------------------------------------------------------------------------------
//	delete an file
//--------------------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/files/models/files.mod.php');
	
	if (array_key_exists('rmfile', $request['args'])) {
		$fileUID = $request['args']['rmfile'];
		if (dbRecordExists('files', $fileUID) == false) { do404(); }
		$f = new File();
		$f->load($fileUID);
		if (authHas($f->data['refModule'], 'files', '') == false) { do403(); }

		$f->delete();

		// dangerous, consider replacing this with something else
		if (array_key_exists('HTTP_REFERER', $_SERVER)) {
			$return = str_replace($serverPath, '', $_SERVER['HTTP_REFERER']);
			do302($return);
		}
	
		do302('/files/');
		
	}

	if ( (array_key_exists('UID', $_POST))
	   AND (dbRecordExists('files', $_POST['UID'])) ) {
	
		$f = new File($_POST['UID']);
		if ($f->data['fileName'] == '') { do404(); }
		if (authHas($f->data['refModule'], 'files', '') == false) { do403(); }	

		$f->delete();
	
		if (array_key_exists('return', $_POST)) {
			do302($_POST['return']);
		}
	
		// TODO: 302 back to wherever the request came from, user may not have permission
		// to view files and could be redirected to a 403.  Confusing.

		do302('/files/');
		
	} else { do404(); }

?>
