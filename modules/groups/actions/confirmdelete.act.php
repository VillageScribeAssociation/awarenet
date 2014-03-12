<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a Groups_Group object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------		
	if (false == array_key_exists('UID', $kapenta->request->args)) { $kapenta->page->do404('Group not specified.'); }

	$model = new Groups_Group($kapenta->request->args['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Group not found.'); }
	if (false == $kapenta->user->authHas('groups', 'groups_group', 'delete', $model->UID))
		{ $kapenta->page->do403('You are not authorized to delete this group.'); }	
	
	//----------------------------------------------------------------------------------------------
	//	make the confirmation block
	//----------------------------------------------------------------------------------------------		
	$labels = array('UID' => $model->UID, 'raUID' => $model->alias);
	$block = $theme->loadBlock('modules/groups/views/confirmdelete.block.php');
	$html = $theme->replaceLabels($labels, $block);
	$kapenta->session->msg($html, 'warn');
	$kapenta->page->do302('groups/' . $model->alias);

?>
