<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show a group's profile page (description, members, announcements, etc)
//--------------------------------------------------------------------------------------------------

	$editUrl = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions (no public users)
	//----------------------------------------------------------------------------------------------
	if (($user->role == 'public') || ($user->role == 'banned')) { $page->do403(); }
	if ('' == $req->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('groups_group');

	$model = new Groups_Group($UID);
	if (false == $model->loaded) { $page->do404('Could not load group.'); }

	//$model->schools->updateSchoolsIndex($model->members->get());

	if (true == $user->authHas('groups', 'groups_group', 'edit', $model->UID)) 
		{ $editUrl = '%%serverPath%%groups/edit/' . $model->alias; }

	//----------------------------------------------------------------------------------------------	
	//	render the page
	//----------------------------------------------------------------------------------------------	
	$page->load('modules/groups/actions/show.page.php');
	$page->blockArgs['raUID'] = $model->alias;
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['groupName'] = $model->name;
	$page->blockArgs['editGroupUrl'] = $editUrl;
	$page->render();

?>
