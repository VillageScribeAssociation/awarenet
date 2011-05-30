<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a Groups_Group object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST variables
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('action not specified'); }
	if ('saveRecord' != $_POST['action']) { $page->do404('action not supported'); } 
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not POSTed'); }

	$UID = $_POST['UID'];

	if (false == $user->authHas('groups', 'groups_group', 'edit', $UID))
		{ $page->do403('You are not authorized to edit this Group.'); }

	//----------------------------------------------------------------------------------------------
	//	load and update the object
	//----------------------------------------------------------------------------------------------
	$model = new Groups_Group($UID);
	if (false == $model->loaded) { $page->do404("Group not found.");}
	//TODO: sanitize description
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'school':	$model->school = $utils->cleanString($value); break;
			case 'name':	$model->name = $utils->cleanString($value); break;
			case 'type':	$model->type = $utils->cleanString($value); break;
			case 'description':	$model->description = $value; break;
		}
	}
	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was saved and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) { $session->msg('Group updated.'); }
	else { $session->msg('Could not save Group:<br/>' . $report); }

	if (true == array_key_exists('return', $_POST)) { $page->do302($_POST['return']); }
	else { $page->do302('/groups/show/' . $model->alias); }

?>
