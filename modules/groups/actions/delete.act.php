<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a Groups_Group object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('deleteRecord' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('Group not specified (UID).'); }
    
	$model = new Groups_Group($_POST['UID']);
	if (false == $user->authHas('groups', 'Groups_Group', 'delete', $model->UID)) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	delete the group and redirect
	//----------------------------------------------------------------------------------------------
	$model->delete();
	$session->msg("Deleted group: " . $model->name);
	$page->do302('groups/');

?>
