<?

//--------------------------------------------------------------------------------------------------
//	iframe to upload multiple images
//--------------------------------------------------------------------------------------------------

	if (array_key_exists('refmodule', $request['args'])) {
	  if (array_key_exists('refuid', $request['args'])) {
	
		$refModule = $request['args']['refmodule'];
		$refUID = $request['args']['refuid'];
		
		$authArgs = array('UID' => $refUID);		
		if (authHas($refModule, 'imageupload', $authArgs) == false) {
			//--------------------------------------------------------------------------------------
			//	not authorised to edit images, just display
			//--------------------------------------------------------------------------------------
			$page->load($installPath . 'modules/images/actions/imageset.if.page.php');
			$page->blockArgs['refModule'] = $refModule;
			$page->blockArgs['refUID'] = $refUID;
			$page->render();
		
		} else {
			//--------------------------------------------------------------------------------------
			//	authorised to edit images, show upload form
			//--------------------------------------------------------------------------------------
			$page->load($installPath . 'modules/images/actions/uploadmultiple.if.page.php');
			$page->blockArgs['refModule'] = $refModule;
			$page->blockArgs['refUID'] = $refUID;
			$page->render();
			
		}
		
	  } else { echo "(UID not specified)"; }
	} else { echo "(module not specified)"; }

?>
