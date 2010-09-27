<?
	//require_once($kapenta->installPath . 'modules/users/models/role.mod.php');
	// ^ sometimes needed for breadcrumbs, etc

//--------------------------------------------------------------------------------------------------
//*	show form to edit a Role object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	$UID = $aliases->findRedirect('Users_Role');
	if (false == $user->authHas('users', 'Users_Role', 'edit', $UID))
		{ $page->do403('You are not authorized to edit this Roles.'); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/users/actions/editrole.page.php');
	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['roleUID'] = $UID;
	$page->blockArgs['raUID'] = $req->ref;
	$page->render();

?>
