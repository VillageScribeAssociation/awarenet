<?

//--------------------------------------------------------------------------------------------------
//	save a gallery entry
//--------------------------------------------------------------------------------------------------

	if ($user->authHas('gallery', 'gallery_gallery', 'edit', 'TODO:UIDHERE') == false) { $page->do403(); }
	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

	if (array_key_exists('action', $_POST)) {

		//------------------------------------------------------------------------------------------
		//	save from 'add child page' form 
		//------------------------------------------------------------------------------------------

		if ($_POST['action'] ==  'addChildPage') {
			$model = new Gallery_Gallery();
			$model->parent = $_POST['UID'];
			$model->title = $_POST['title'];
			$model->description = $_POST['description'];
			$model->save();
			$page->do302('gallery/' . $model->alias);
		}

		//------------------------------------------------------------------------------------------
		//	save from edit form
		//------------------------------------------------------------------------------------------

		if ($_POST['action'] == 'saveRecord') {
			$model = new Gallery_Gallery($_POST['UID']);
			$model->title = $_POST['title'];
			$model->description = $_POST['description'];
			$model->save();
			$_SESSION['sMessage'] .= "Saved changes to gallery.<br/>\n";
			$page->do302('gallery/' . $model->alias);			
		}

	}

?>
