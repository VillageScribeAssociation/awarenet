<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a Groups_Group object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('deleteRecord' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('Group not specified (UID).'); }
    
	$model = new Groups_Group($_POST['UID']);
	if (false == $kapenta->user->authHas('groups', 'groups_group', 'delete', $model->UID)) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	delete the group and redirect
	//----------------------------------------------------------------------------------------------
	$model->delete();
	$kapenta->session->msg("Deleted group: " . $model->name);
	$kapenta->page->do302('groups/');

?>
