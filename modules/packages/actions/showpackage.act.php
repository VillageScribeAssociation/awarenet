<?

	require_once($kapenta->installPath . 'modules/packages/inc/kupdatemanager.class.php');

//--------------------------------------------------------------------------------------------------
//*	displays details and install status of a package
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if ('' == $req->ref) { $page->do404(); }

	$updateManager = new KUpdateManager();

	if (false == $updateManager->isInstalled($req->ref)) {

		$req->ref = $updateManager->findByName($req->ref);

		if (('' == $req->ref) || (false == $updateManager->isInstalled($req->ref))) {
			$kapenta->page->do404('Package not installed.');
		}
	}

	$meta = $updateManager->getPackageDetails($req->ref);
	if (0 == count($meta)) { $kapenta->page->do404('Unknown package.'); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/packages/actions/showpackage.page.php');
	$kapenta->page->blockArgs['packageUID'] = $meta['uid'];
	$kapenta->page->blockArgs['packageName'] = $meta['name'];
	$kapenta->page->blockArgs['UID'] = $meta['uid'];
	$kapenta->page->blockArgs['name'] = $meta['name'];
	$kapenta->page->blockArgs['source'] = $meta['source'];
	$kapenta->page->blockArgs['status'] = $meta['status'];
	$page->render();

?>
