<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add a new group
//--------------------------------------------------------------------------------------------------

	if (false == $user->authHas('groups', 'groups_group', 'new', '')) 
		{ $page->do403('You are not authorized to create new groups.'); }

	$model = new Groups_Group();

	$model->save();
	
	$page->do302('groups/edit/' . $model->UID);

?>
