<?

	//require_once($kapenta->installPath . 'modules/home/models/partner.mod.php');
	// ^ sometimes needed for breadcrumbs, etc

//--------------------------------------------------------------------------------------------------
//*	show form to edit a Partner object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	$UID = $aliases->findRedirect('home_partner');
	if (false == $kapenta->user->authHas('home', 'home_partner', 'edit', $UID))
		{ $kapenta->page->do403('You are not authorized to edit this Partners.'); }


	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/home/actions/editpartner.page.php');
	$kapenta->page->blockArgs['UID'] = $UID;
	$kapenta->page->blockArgs['partnerUID'] = $UID;
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->render();

?>
