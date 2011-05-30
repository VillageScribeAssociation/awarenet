<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a group record
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	$UID = $aliases->findRedirect('groups_group');

	$model = new Groups_Group($UID);
	if (false == $model->loaded) { $page->do404('Could not load group.'); }
	if (false == $user->authHas('groups', 'groups_group', 'edit', $model->UID))
		{ $page->do403('You cannot edit the group.'); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/groups/actions/edit.page.php');
	$page->blockArgs['raUID'] = $model->alias;
	$page->blockArgs['viewGroupUrl'] = '%%serverPath%%groups/' . $model->alias;
	$page->render();

?>
