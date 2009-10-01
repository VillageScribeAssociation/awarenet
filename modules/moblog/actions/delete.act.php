<?

//--------------------------------------------------------------------------------------------------
//	delete a moblog post
//--------------------------------------------------------------------------------------------------

	if (authHas('moblog', 'edit', '') == false) { do403(); }

	if ( (array_key_exists('action', $_POST)) 
	  AND ($_POST['action'] == 'deleteRecord') 
	  AND (array_key_exists('UID', $_POST)) 
	  AND (dbRecordExists('moblog', $_POST['UID'])) ) {

		//-----------------------------------------------------------------------------------------
		//	load the post in question
		//-----------------------------------------------------------------------------------------
	  
		require_once($installPath . 'modules/moblog/models/moblog.mod.php');
	  
		$model = new Moblog();
		$model->load($_POST['UID']);
		
		//-----------------------------------------------------------------------------------------
		//	check that user has authority to delete this post
		//-----------------------------------------------------------------------------------------

		$authorised = false;
		if ($user->data['ofGroup'] == 'admin') { $authorised = true; }
		if ($user->data['UID'] == $model->data['createdBy']) { $authorised = true; }		
		if ($authorised == false) { do403(); }

		//-----------------------------------------------------------------------------------------
		//	delete it
		//-----------------------------------------------------------------------------------------

		$_SESSION['sMessage'] .= "Deleted moblog post: " . $model->data['title'] . "<br/>\n";
		$model->delete();
		do302('moblog/');
	  
	} else { do404(); }

?>
