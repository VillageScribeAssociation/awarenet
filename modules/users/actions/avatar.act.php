<?

//--------------------------------------------------------------------------------------------------
//*	alias of /images/first/ specifically for user images
//--------------------------------------------------------------------------------------------------
//ref: UID or alias of a Users_User object [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404("User UID or alias not given."); }

	$model = new Users_User($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('Unknown user.'); }

	$kapenta->request->args['module'] = 'users';
	$kapenta->request->args['model'] = 'users_user';
	$kapenta->request->args['uid'] = $model->UID;

	include $kapenta->installPath . 'modules/images/actions/first.act.php';

?>
