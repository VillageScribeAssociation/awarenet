<?

//--------------------------------------------------------------------------------------------------
//	add a new (root) gallery
//--------------------------------------------------------------------------------------------------

	if (authHas('gallery', 'edit', '') == false) { do403(); }
	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

	if ((array_key_exists('action', $_POST) == true) && ($_POST['action'] == 'createGallery')) {
		//------------------------------------------------------------------------------------------
		//	create a gallery given a title
		//------------------------------------------------------------------------------------------
		$title = clean_string($_POST['title']);

		if ($title == '') {
			//--------------------------------------------------------------------------------------
			//	invalid title
			//--------------------------------------------------------------------------------------
			$_SESSION['sMessage'] = "Please choose a title for your new gallery.<br/>\n";
			do302('gallery/list/' . $user->data['recordAlias']);

		} else {
			//--------------------------------------------------------------------------------------
			//	create gallery
			//--------------------------------------------------------------------------------------
			$model = new Gallery();
			$model->data['UID'] = createUID();
			$model->data['title'] = $title;
			$model->save();

			do302('gallery/edit/' . $model->data['UID']);

		}


	} else {
		//------------------------------------------------------------------------------------------
		//	just create a gallery
		//------------------------------------------------------------------------------------------
		$model = new Gallery();
		$model->data['UID'] = createUID();
		$model->save();

	}
	
	do302('gallery/edit/' . $model->data['recordAlias']);

?>
