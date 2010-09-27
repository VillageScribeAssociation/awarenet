<?
	//require_once($kapenta->installPath . 'modules/badges/models/badge.mod.php');
	// ^ sometimes needed for breadcrumbs, etc

//--------------------------------------------------------------------------------------------------
//*	show form to edit a Badge object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	$UID = $aliases->findRedirect('Badges_Badge');
	if (false == $user->authHas('badges', 'Badges_Badge', 'edit', $UID))
		{ $page->do403('You are not authorized to edit this Badges.'); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/badges/actions/edit.page.php');
	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['badgeUID'] = $UID;
	$page->blockArgs['raUID'] = $req->ref;
	$page->render();

?>
