<?

//--------------------------------------------------------------------------------------------------
//*	video module settings
//--------------------------------------------------------------------------------------------------
//+	Allowed image dimensions are stored in registry keys as either fixed size or fixed width.

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	define default file associations
	//----------------------------------------------------------------------------------------------

	$assoc = array('flv', 'mp4', 'mp3');

	//----------------------------------------------------------------------------------------------
	//	set default file associations with this module
	//----------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('resetFileAssoc' == $_POST['action'])) {
		$reg = $registry->search('live', 'live.file.');

		//	delete existing file associations with this module
		foreach($reg as $key => $value) {
			if ('videos' == $value) { $registry->delete($key); }
		}

		//	recreate defaults
		foreach($assoc as $ext) { $registry->set('live.file.' . $ext, 'videos'); }
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/videos/actions/settings.page.php');
	$page->render();

?>
