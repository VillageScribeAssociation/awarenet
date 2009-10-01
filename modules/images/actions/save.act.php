<?

	require_once($installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//	save changes to an image
//--------------------------------------------------------------------------------------------------

	if ( (array_key_exists('action', $_POST))
	   AND ($_POST['action'] == 'saveImage')
	   AND (array_key_exists('UID', $_POST)) ) {

		//------------------------------------------------------------------------------------------
		//	check reference and authorisation
		//------------------------------------------------------------------------------------------
	   
		$i = new Image($_POST['UID']);
		if ($i->data['fileName'] == '') { do404(); }
		
		$authArgs = array('UID' => $i->data['refUID']);
		if (authHas($i->data['refModule'], 'images', '') == false) { return false; }

		//------------------------------------------------------------------------------------------
		//	make the changes
		//------------------------------------------------------------------------------------------
		
		$i->data['title'] = $_POST['title'];
		$i->data['caption'] = $_POST['caption'];
		$i->data['licence'] = $_POST['licence'];
		$i->data['attribName'] = $_POST['attribName'];
		$i->data['attribURL'] = $_POST['attribURL'];
		if (is_numeric($_POST['weight']) == true) {	$i->data['weight'] = $_POST['weight']; }
		
		$i->save();
		
		//------------------------------------------------------------------------------------------
		//	redirect back
		//------------------------------------------------------------------------------------------
		
		if (array_key_exists('return', $_POST)) {
			if ($_POST['return'] == 'uploadmultiple') {
				$retUrl = 'images/uploadmultiple/refModule_' . $i->data['refModule'] 
					. '/refUID_' . $i->data['refUID'] . '/';
				do302($retUrl);
			}
		}
		do302('images/edit/' . $i->data['recordAlias']);
	 
	}

?>
