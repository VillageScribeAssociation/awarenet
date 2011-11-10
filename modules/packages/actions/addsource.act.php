<?

	require_once($kapenta->installPath . 'modules/packages/inc/kupdatemanager.class.php');

//--------------------------------------------------------------------------------------------------
//*	add a software source (kapenta repository)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('addSource' != $_POST['action']) { $page->do404('Action not recognized.'); }
	if (false == array_key_exists('source', $_POST)) { $page->do404('Source not given.'); }

	$source = trim($_POST['source']);

	//----------------------------------------------------------------------------------------------
	//	try add the source
	//----------------------------------------------------------------------------------------------
	if ('' != $source) {
		$updateManager = new KUpdateManager();
		$check = $updateManager->addSource($source);

		if (true == $check) {
			$session->msg('Added source: ' . $source);
		} else {
			$session->msg('Could not add source: ' . $source . ' (unkown error)');
		}

	} else {
		$session->msg('No source given.');
	}	

	//----------------------------------------------------------------------------------------------
	//	redirect back to package manager
	//----------------------------------------------------------------------------------------------
	$page->do302('packages/');

?>
