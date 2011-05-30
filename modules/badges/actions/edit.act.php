<?
	//require_once($kapenta->installPath . 'modules/badges/models/badge.mod.php');
	// ^ sometimes needed for breadcrumbs, etc

//--------------------------------------------------------------------------------------------------
//*	show form to edit a Badge object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	$UID = $aliases->findRedirect('badges_badge');
	if (false == $user->authHas('badges', 'badges_badge', 'edit', $UID))
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
