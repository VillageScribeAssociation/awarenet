<?

//--------------------------------------------------------------------------------------------------
//*	alias of /images/first/ specifically for user images
//--------------------------------------------------------------------------------------------------
//ref: UID or alias of a Users_User object [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404("User UID or alias not given."); }

	$model = new Users_User($req->ref);
	if (false == $model->loaded) { $page->do404('Unknown user.'); }

	$req->args['module'] = 'users';
	$req->args['model'] = 'users_user';
	$req->args['uid'] = $model->UID;

	include $kapenta->installPath . 'modules/images/actions/first.act.php';

?>
