<?

//--------------------------------------------------------------------------------------------------
//*	delete a user record *WANRING* will delete all data they have added (blog, comments, etc)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	authorization - only admins can delete a user account
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	OK, delete it
	//----------------------------------------------------------------------------------------------
	$model = new Users_User($kapenta->request->ref);
	$_SESSION['sMessage'] .= "Deleted user: " . $model->getName() . "<br/>";
	$model->delete();

	$page->do302('users/');

?>
