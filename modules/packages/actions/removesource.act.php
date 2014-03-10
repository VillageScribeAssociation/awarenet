<?

	require_once($kapenta->installPath . 'modules/packages/inc/kupdatemanager.class.php');

//--------------------------------------------------------------------------------------------------
//*	remove a software source from the kapenta package manager (kapenta.sources.list)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if (false == array_key_exists('source', $kapenta->request->args)) { $page->do404('Source not given.'); }

	$source = base64_decode($kapenta->request->args['source']);

	//----------------------------------------------------------------------------------------------
	//	remove the source
	//----------------------------------------------------------------------------------------------
	$updateManager = new KUpdateManager();	
	if (true == $updateManager->hasSource($source)) {
		$check = $updateManager->removeSource($source);
		if (true == $check) { 
			$session->msg("Removed software source:<br/> $source", 'ok');
		} else {
			$session->msg("Could not remove software source:<br/> $source", 'warn');
		}

	} else {
		$session->msg("Unkown source: $source", 'bad');
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to packages console
	//----------------------------------------------------------------------------------------------
	$page->do302('packages/');
	
?>
