<?

//--------------------------------------------------------------------------------------------------
//	save submitted block, return user to to /pages/list
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); } // admins only

	if ( (array_key_exists('action', $_POST) == true)
		 AND ($_POST['action'] == 'saveBlock') 
		 AND (array_key_exists('module', $_POST) == true)
		 AND (array_key_exists('block', $_POST) == true) ) {

		//-----------------------------------------------------------------------------------------
		//	save the block
		//-----------------------------------------------------------------------------------------
		$fileName = 'modules/'. $_POST['module'] .'/views/'. $_POST['block'] .'.block.php';
		$fileName = str_replace('..', '', $fileName);

		if (file_exists($installPath . $fileName)) {

			saveBlock($fileName, stripslashes($_POST['blockContent']));

		} else { $_SESSION['sMessage'] .= "no such block :-(<br/>\n"; }

		$page->do302('pages/list/');

	} else { $page->do404(); }

?>
