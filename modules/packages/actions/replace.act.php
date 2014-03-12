<?

	require_once($kapenta->installPath . 'modules/packages/inc/kupdatemanager.class.php');

//--------------------------------------------------------------------------------------------------
//*	update a single file from the repository
//--------------------------------------------------------------------------------------------------
//postarg: packageUID - UID of a KPackage [string]
//postarg: fileUID - UID of a file in this package [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403('Admins only.'); }
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('replaceFile' != $_POST['action']) { $kapenta->page->do404('Action not recognized'); }

	if (false == array_key_exists('packageUID', $_POST)) { $kapenta->page->do404('Package not specified.'); }
	if (false == array_key_exists('fileUID', $_POST)) { $kapenta->page->do404('File not specified.'); }

	$packageUID = $_POST['packageUID'];
	$fileUID = $_POST['fileUID'];

	$um = new KUpdateManager();
	if (false == $um->isInstalled($packageUID)) { $kapenta->page->do404('Unknown package'); }

	$package = new KPackage($packageUID);
	if (false == array_key_exists($fileUID, $package->files)) { $kapenta->page->do404('File not found.'); }

	//----------------------------------------------------------------------------------------------
	//	get the file
	//----------------------------------------------------------------------------------------------
	$meta = $package->files[$fileUID];
	$check = $package->updateFile($fileUID);
	if (true == $check) {
		$kapenta->session->msg('Updated file: ' . $meta['path'] . '.', 'ok');
	} else {
		$kapenta->session->msg('Could not update file: ' . $meta['path'] . '.', 'bad');
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to package listing
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('packages/showpackage/' . $package->UID);

?>
