<?

	require_once($kapenta->installPath . 'modules/packages/inc/kpackage.class.php');

//--------------------------------------------------------------------------------------------------
//*	update local files from repository
//--------------------------------------------------------------------------------------------------
//postarg: packageUID - UID of an installed package [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not given.'); }
	if ('checkout' != $_POST['action']) { $kapenta->page->do404('Action not recognized.'); }
	if (false == array_key_exists('packageUID', $_POST)) { $kapenta->page->do404('Package UID not given'); }

	$packageUID = $_POST['packageUID'];

	$package = new KPackage($packageUID);
	if (false == $package->loaded) { $kapenta->page->do404('Could not load package.'); }

	//----------------------------------------------------------------------------------------------
	//	get all the files
	//----------------------------------------------------------------------------------------------
	$changeCount = 0;
	$ignoreCount = 0;
	$toRetry = array();

	foreach($package->files as $pf) {
		$download = false;
		if (false == $kapenta->fs->exists($pf['path'])) { $download = true; }
		else {
			if ($pf['hash'] != $package->getFileHash($pf['path'])) { $download = true; }
		}

		if (true == $download) { 
			$check = $package->updateFile($pf['uid']);
			if (true == $check) {
				$session->msg('Updated: ' . $pf['path'], 'ok');
				$changeCount++;
			} else {
				$session->msg('Could not update: ' . $pf['path'], 'bad');
				$toRetry[] = $pf;
			}
		} else {
			$ignoreCount++;
		}
	}

	$msg = "Updated $changeCount files, $ignoreCount unchanged, " . count($toRetry) . " failed.";
	$session->msg($msg, 'ok');	

	$package->getLocalDifferent();		//	reset dirty flag in registry for this package

	//----------------------------------------------------------------------------------------------
	//	retry any which failed
	//----------------------------------------------------------------------------------------------
	//TODO: this

	//----------------------------------------------------------------------------------------------
	//	return to package listing
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('packages/showpackage/' . $package->UID);

?>
