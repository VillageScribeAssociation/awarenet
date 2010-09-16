<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add a new (root) gallery
//--------------------------------------------------------------------------------------------------
//TODO: replace with standard generated code

	if (false == $user->authHas('gallery', 'Gallery_Gallery', 'new')) { $page->do403(); }

	if ((true == array_key_exists('action', $_POST)) && ('createGallery' == $_POST['action'])) {
		//------------------------------------------------------------------------------------------
		//	create a gallery given a title
		//------------------------------------------------------------------------------------------
		$title = $utils->cleanString($_POST['title']);

		if ('' == $title) {
			//--------------------------------------------------------------------------------------
			//	invalid title
			//--------------------------------------------------------------------------------------
			$_SESSION['sMessage'] = "Please choose a title for your new gallery.<br/>\n";
			$page->do302('gallery/list/' . $user->alias);

		} else {
			//--------------------------------------------------------------------------------------
			//	create gallery
			//--------------------------------------------------------------------------------------
			$model = new Gallery_Gallery();
			$model->title = $title;
			$model->save();

			$page->do302('gallery/edit/' . $model->UID);

		}


	} else {
		//------------------------------------------------------------------------------------------
		//	just create a gallery
		//------------------------------------------------------------------------------------------
		$model = new Gallery_Gallery();
		$model->save();

	}
	
	$page->do302('gallery/edit/' . $model->alias);

?>
