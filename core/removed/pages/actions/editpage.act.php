<?

//--------------------------------------------------------------------------------------------------------------
//	edit a page
//--------------------------------------------------------------------------------------------------------------

	if ((array_key_exists('module', $req->args)) AND ('' != $req->ref)) {
		//----------------------------------------------------------------------------------------------
		// TODO: more error checking here (directory traversal, etc)
		//----------------------------------------------------------------------------------------------
		$fileName = $installPath . 'modules/' . $req->args['module'] . '/actions/' . $req->ref;
		if (file_exists($fileName)) {
			$page->blockArgs['xmodule'] = $req->args['module'];
			$page->blockArgs['xpage'] = $req->ref;
			$page->load('modules/pages/actions/edit.page.php');
			$page->render();
		}
	}

?>
