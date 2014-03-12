<?

//--------------------------------------------------------------------------------------------------
//*	video module settings
//--------------------------------------------------------------------------------------------------
//+	Allowed image dimensions are stored in registry keys as either fixed size or fixed width.

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	define default file associations
	//----------------------------------------------------------------------------------------------

	$assoc = array('flv', 'mp4', 'mp3', 'swf');

	//----------------------------------------------------------------------------------------------
	//	set default file associations with this module
	//----------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('resetFileAssoc' == $_POST['action'])) {
		$reg = $kapenta->registry->search('live', 'live.file.');

		//	delete existing file associations with this module
		foreach($reg as $key => $value) {
			if ('videos' == $value) { $kapenta->registry->delete($key); }
		}

		//	recreate defaults
		foreach($assoc as $ext) { $kapenta->registry->set('live.file.' . $ext, 'videos'); }
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/videos/actions/settings.page.php');
	$kapenta->page->render();

?>
