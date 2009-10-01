<?

//--------------------------------------------------------------------------------------------------
//	iframe to upload multiple files
//--------------------------------------------------------------------------------------------------

	if (array_key_exists('refmodule', $request['args'])) {
	  if (array_key_exists('refuid', $request['args'])) {
	
		$refModule = $request['args']['refmodule'];
		$refUID = $request['args']['refuid'];
		
		$authArgs = array('UID' => $refUID);		
		if (authHas($refModule, 'files', $authArgs) == false) {
			//--------------------------------------------------------------------------------------
			//	not authorised to edit files, just display
			//--------------------------------------------------------------------------------------
			$page->load($installPath . 'modules/files/actions/fileset.if.page.php');
			$page->blockArgs['refModule'] = $refModule;
			$page->blockArgs['refUID'] = $refUID;
			$page->render();
		
		} else {
			//--------------------------------------------------------------------------------------
			//	authorised to edit files, show upload form
			//--------------------------------------------------------------------------------------
			$page->load($installPath . 'modules/files/actions/uploadmultiple.if.page.php');
			$page->blockArgs['refModule'] = $refModule;
			$page->blockArgs['refUID'] = $refUID;
			$page->render();
			
		}
		
	  } else { echo "(UID not specified)"; }
	} else { echo "(module not specified)"; }

?>
