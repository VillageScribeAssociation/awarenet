<?
	//require_once($kapenta->installPath . 'modules/newsletter/models/adunit.mod.php');
	// ^ sometimes needed for breadcrumbs, etc

//--------------------------------------------------------------------------------------------------
//*	show form to edit a Adunit object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404('Adunit not specified'); }
	$UID = $kapenta->request->ref;
	if (false == $kapenta->db->objectExists('newsletter_adunit', $UID)) { $page->do404(); }
	if (false == $user->authHas('newsletter', 'newsletter_adunit', 'edit', $UID))
		{ $page->do403('You are not authorized to edit this Adunits.'); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/newsletter/actions/editadunit.page.php');
	$kapenta->page->blockArgs['UID'] = $UID;
	$kapenta->page->blockArgs['adunitUID'] = $UID;
	$kapenta->page->render();

?>
