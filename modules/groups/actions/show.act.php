<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show a group's profile page (description, members, announcements, etc)
//--------------------------------------------------------------------------------------------------

	$editUrl = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions (no public users)
	//----------------------------------------------------------------------------------------------
	if (($user->role == 'public') || ($user->role == 'banned')) { $kapenta->page->do403(); }
	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }
	$UID = $aliases->findRedirect('groups_group');

	$model = new Groups_Group($UID);
	if (false == $model->loaded) { $kapenta->page->do404('Could not load group.'); }

	//$model->schools->updateSchoolsIndex($model->members->get());

	if (true == $user->authHas('groups', 'groups_group', 'edit', $model->UID)) 
		{ $editUrl = '%%serverPath%%groups/edit/' . $model->alias; }

	//----------------------------------------------------------------------------------------------	
	//	render the page
	//----------------------------------------------------------------------------------------------	
	$kapenta->page->load('modules/groups/actions/show.page.php');
	$kapenta->page->blockArgs['raUID'] = $model->alias;
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['groupName'] = $model->name;
	$kapenta->page->blockArgs['editGroupUrl'] = $editUrl;
	$kapenta->page->render();

?>
