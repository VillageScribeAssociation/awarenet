<?

//--------------------------------------------------------------------------------------------------
//	delete an image and associated record, derivative files, etc
//--------------------------------------------------------------------------------------------------

	if ( (array_key_exists('UID', $_POST))
	   AND (dbRecordExists('images', $_POST['UID'])) ) {
	
		require_once($installPath . 'modules/images/models/image.mod.php');
		$i = new Image($_POST['UID']);
		if ($i->data['fileName'] == '') { do404(); }
		if (authHas($i->data['refModule'], 'images', '') == false) { do403(); }
	
		$i->delete();
	
		if (array_key_exists('return', $_POST)) {
			if ($_POST['return'] == 'xml') {
				echo "<?xml version=\"1.0\"?>\n";
				echo "<notice>Image " . $i->data['UID'] . " deleted</notice>\n";
				die();
			} else {
				do302($_POST['return']);
			}
		}
	
		do302('/images/');
		
	} else { 
		if ((array_key_exists('return', $_POST)) && ($_POST['return'] == 'xml')) {
				echo "<?xml version=\"1.0\"?>\n";
				echo "<notice>Image " . clean_string($_POST['UID']) . " not found</notice>\n";
				die();
		} else { do404(); }
	}

?>
