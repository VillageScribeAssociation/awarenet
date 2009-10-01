<?

//--------------------------------------------------------------------------------------------------
//	edit a block
//--------------------------------------------------------------------------------------------------

	if ($user->data['ofGroup'] != 'admin') { do404(); } // admins only

	if ((array_key_exists('module', $request['args'])) AND ($request['ref'] != '')) {
		//------------------------------------------------------------------------------------------
		// TODO: more error checking here (directory traversal, etc)
		//------------------------------------------------------------------------------------------
		$fileName = $installPath . 'modules/' . $request['args']['module'] . '/' . $request['ref'];
		if (file_exists($fileName)) {
			$page->load($installPath . 'modules/blocks/actions/edit.page.php');
			$page->blockArgs['xmodule'] = $request['args']['module'];
			$page->blockArgs['xblock'] = $request['ref'];
			$page->render();
		}
	}

?>
