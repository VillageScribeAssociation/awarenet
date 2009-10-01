<?

//--------------------------------------------------------------------------------------------------
//	save a gallery entry
//--------------------------------------------------------------------------------------------------

	if (authHas('gallery', 'edit', '') == false) { do403(); }
	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

	if (array_key_exists('action', $_POST)) {

		//------------------------------------------------------------------------------------------
		//	save from 'add child page' form 
		//------------------------------------------------------------------------------------------

		if ($_POST['action'] ==  'addChildPage') {
			$model = new Gallery();
			$model->data['parent'] = $_POST['UID'];
			$model->data['title'] = $_POST['title'];
			$model->data['description'] = $_POST['description'];
			$model->save();
			do302('gallery/' . $model->data['recordAlias']);
		}

		//------------------------------------------------------------------------------------------
		//	save from edit form
		//------------------------------------------------------------------------------------------

		if ($_POST['action'] == 'saveRecord') {
			$model = new Gallery($_POST['UID']);
			$model->data['title'] = $_POST['title'];
			$model->data['description'] = $_POST['description'];
			$model->save();
			$_SESSION['sMessage'] .= "Saved changes to gallery.<br/>\n";
			do302('gallery/' . $model->data['recordAlias']);			
		}

	}

?>
