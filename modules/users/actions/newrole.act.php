<?
	require_once($kapenta->installPath . 'modules/users/models/role.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new Role object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//*	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('users', 'users_role', 'new'))
		{ $kapenta->page->do403('You are not authorized to create new Roles.'); }


	//----------------------------------------------------------------------------------------------
	//*	create the object
	//----------------------------------------------------------------------------------------------
	$model = new Users_Role();
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'name':	$model->name = $utils->cleanString($value); break;
			case 'description':	$model->description = $utils->cleanString($value); break;
			case 'permissions':	$model->permissions = $utils->cleanString($value); break;
			case 'alias':	$model->alias = $utils->cleanString($value); break;
		}
	}
	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//*	check that object was created and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$session->msg('New Role<br/>');
		$kapenta->page->do302('/users/editrole/' . $model->alias);
	} else {
		$session->msg('Could not create new Role:<br/>' . $report);
	}

?>
