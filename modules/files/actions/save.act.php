<?

	require_once($installPath . 'modules/files/models/files.mod.php');

//--------------------------------------------------------------------------------------------------
//	save changes to an file
//--------------------------------------------------------------------------------------------------

	if ( (array_key_exists('action', $_POST))
	   AND ($_POST['action'] == 'savefile')
	   AND (array_key_exists('UID', $_POST)) ) {

		//------------------------------------------------------------------------------------------
		//	check reference and authorisation
		//------------------------------------------------------------------------------------------
	   
		$f = new File($_POST['UID']);
		if ($f->data['fileName'] == '') { do404(); }
		
		$authArgs = array('UID' => $f->data['refUID']);
		if (authHas($f->data['refModule'], 'files', '') == false) { return false; }

		//------------------------------------------------------------------------------------------
		//	make the changes
		//------------------------------------------------------------------------------------------
		
		$f->data['title'] = $_POST['title'];
		$f->data['caption'] = $_POST['caption'];
		$f->data['licence'] = $_POST['licence'];
		$f->data['attribName'] = $_POST['attribName'];
		$f->data['attribURL'] = $_POST['attribURL'];
		
		$f->save();
		
		//------------------------------------------------------------------------------------------
		//	redirect back
		//------------------------------------------------------------------------------------------
		
		if (array_key_exists('return', $_POST)) {
			if ($_POST['return'] == 'uploadmultiple') {
				$retUrl = 'files/uploadmultiple/refModule_' . $f->data['refModule'] 
					. '/refUID_' . $f->data['refUID'] . '/';
				do302($retUrl);
			}
		}
		do302('files/edit/' . $f->data['recordAlias']);
	}

?>
