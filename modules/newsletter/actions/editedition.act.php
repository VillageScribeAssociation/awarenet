<?
	//require_once($kapenta->installPath . 'modules/newsletter/models/edition.mod.php');
	// ^ sometimes needed for breadcrumbs, etc

//--------------------------------------------------------------------------------------------------
//*	show form to edit a Edition object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	$UID = $aliases->findRedirect('newsletter_edition');
	if (false == $kapenta->user->authHas('newsletter', 'newsletter_edition', 'edit', $UID))
		{ $kapenta->page->do403('You are not authorized to edit this Editions.'); }


	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/newsletter/actions/editedition.page.php');
	$kapenta->page->blockArgs['UID'] = $UID;
	$kapenta->page->blockArgs['editionUID'] = $UID;
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->render();

?>
