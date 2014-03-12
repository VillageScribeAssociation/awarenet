<?
	//require_once($kapenta->installPath . 'modules/code/models/package.mod.php');
	// ^ sometimes needed for breadcrumbs, etc

//--------------------------------------------------------------------------------------------------
//*	show form to edit a Package object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	$UID = $aliases->findRedirect('code_package');
	if (false == $kapenta->user->authHas('code', 'code_package', 'edit', $UID)) { 
		$kapenta->page->do403('You are not authorized to edit this Packages.'); 
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/code/actions/editpackage.page.php');
	$kapenta->page->blockArgs['UID'] = $UID;
	$kapenta->page->blockArgs['packageUID'] = $UID;
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->render();

?>
