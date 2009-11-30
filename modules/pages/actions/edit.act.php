<?

//--------------------------------------------------------------------------------------------------------------
//	edit a page
//--------------------------------------------------------------------------------------------------------------

	if ((array_key_exists('module', $request['args'])) AND ($request['ref'] != '')) {
		//----------------------------------------------------------------------------------------------
		// TODO: more error checking here (directory traversal, etc)
		//----------------------------------------------------------------------------------------------
		$fileName = $installPath . 'modules/' . $request['args']['module'] . '/actions/' . $request['ref'];
		if (file_exists($fileName)) {
			$page->blockArgs['xmodule'] = $request['args']['module'];
			$page->blockArgs['xpage'] = $request['ref'];
			$page->load($installPath . 'modules/pages/actions/edit.page.php');
			$page->render();
		}
	}

?>
