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
	if (false == $updateManager->isInstalled($req->ref)) { $page->do404('Package not installed.'); }

	$meta = $updateManager->getPackageDetails($req->ref);
	if (0 == count($meta)) { $page->do404('Unknown package.'); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/packages/actions/showpackage.page.php');
	$page->blockArgs['packageUID'] = $meta['UID'];
	$page->blockArgs['packageName'] = $meta['name'];
	$page->blockArgs['UID'] = $meta['UID'];
	$page->blockArgs['name'] = $meta['name'];
	$page->blockArgs['source'] = $meta['source'];
	$page->blockArgs['status'] = $meta['status'];
	$page->render();

?>
