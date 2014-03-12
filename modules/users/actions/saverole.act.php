<?

	require_once($kapenta->installPath . 'modules/users/models/role.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to an Role object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST variables
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('action not specified'); }
	if ('saveRole' != $_POST['action']) { $kapenta->page->do404('action not supported'); } 
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('UID not POSTed'); }

	$UID = $_POST['UID'];

	if (false == $kapenta->user->authHas('users', 'users_role', 'edit', $UID))
		{ $kapenta->page->do403('You are not authorized to edit this Role.'); }

	//----------------------------------------------------------------------------------------------
	//	load and update the object
	//----------------------------------------------------------------------------------------------
	$model = new Users_Role($UID);
	if (false == $model->loaded) { $kapenta->page->do404("could not load Role $UID");}

	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'name':	$model->name = $utils->cleanString($value); break;
			case 'description':	$model->description = $utils->cleanString($value); break;
			case 'permissions':	$model->permissions = $utils->cleanString($value); break;
		}
	}
	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was saved and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$kapenta->session->msg('New Role<br/>');
	} else {
		$kapenta->session->msg('Could not save Role:<br/>' . $report);
	}

	if (true == array_key_exists('return', $_POST)) { $kapenta->page->do302($_POST['return']); }
	else { $kapenta->page->do302('/users/showrole/' . $model->alias); }

?>
