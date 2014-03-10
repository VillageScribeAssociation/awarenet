<?

	require_once($kapenta->installPath . 'modules/packages/inc/kupdatemanager.class.php');

//--------------------------------------------------------------------------------------------------
//*	displays details and install status of a package
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if ('' == $kapenta->request->ref) { $page->do404(); }

	$updateManager = new KUpdateManager();

	if (false == $updateManager->isInstalled($kapenta->request->ref)) {

		$kapenta->request->ref = $updateManager->findByName($kapenta->request->ref);

		if (('' == $kapenta->request->ref) || (false == $updateManager->isInstalled($kapenta->request->ref))) {
			$kapenta->page->do404('Package not installed.');
		}
	}

	$meta = $updateManager->getPackageDetails($kapenta->request->ref);
	if (0 == count($meta)) { $kapenta->page->do404('Unknown package.'); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/packages/actions/showpackage.page.php');
	$kapenta->page->blockArgs['packageUID'] = $meta['uid'];
	$kapenta->page->blockArgs['packageName'] = $meta['name'];
	$kapenta->page->blockArgs['UID'] = $meta['uid'];
	$kapenta->page->blockArgs['name'] = $meta['name'];
	$kapenta->page->blockArgs['source'] = $meta['source'];
	$kapenta->page->blockArgs['status'] = $meta['status'];
	$kapenta->page->render();

?>
