<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a Groups_Group object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------		
	if (false == array_key_exists('uid', $req->args)) { $page->do404(); }

	$model = new Groups_Group($req->args['uid']);
	if (false == $model->loaded) { $page->do404('Group not found.'); }
	if (false == $user->authHas('groups', 'Groups_Group', 'delete', $model->UID))
		{ $page->do403('You are not authorized to delete this group.'); }	
	
	//----------------------------------------------------------------------------------------------
	//	make the confirmation block
	//----------------------------------------------------------------------------------------------		
	$labels = array('UID' => $model->UID, 'raUID' => $model->alias);
	$block = $theme->loadBlock('modules/groups/views/confirmdelete.block.php');
	$html = $theme->replaceLabels($labels, );
	$session->msg($html, 'warn');
	$page->do302('groups/' . $model->alias);

?>
