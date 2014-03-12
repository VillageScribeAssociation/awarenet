<?

	require_once($kapenta->installPath . 'modules/code/models/userindex.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/package.mod.php');

//-------------------------------------------------------------------------------------------------
//*	grants a user privileges on a packge
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	check permissions and POST vars
	//---------------------------------------------------------------------------------------------
	if ($kapenta->user->role != 'admin') { $kapenta->page->do403(); } // only admins can do this
	//TODO: permissions check here

	if (false == array_key_exists('packageUID', $_POST)) { $kapenta->page->do404('Package not specified.'); }
	if (false == $kapenta->db->objectExists('code_package', $_POST['packageUID'])) { $kapenta->page->do404(); }

	if (false == array_key_exists('user', $_POST)) { $kapenta->page->do404('User not specified.'); }

	$codeuser = new Users_User($_POST['user']);
	if (false == $codeuser->loaded) { $kapenta->page->do404('Unkown user'); }

	//---------------------------------------------------------------------------------------------
	//	grant the permission
	//---------------------------------------------------------------------------------------------
	$model = new Code_UserIndex();
	$model->packageUID = $_POST['packageUID'];
	$model->userUID = $codeuser->UID;
	$model->privilege = 'commit';
	$report = $model->save();

	//---------------------------------------------------------------------------------------------
	//	redirect back to user list
	//---------------------------------------------------------------------------------------------	
	if ('' == $report) { 
		$nameLink = '[[:users::namelink::userUID=' . $codeuser->UID . ':]]';
		$msg = "Granted $nameLink permissions on this package.";
		$kapenta->session->msg($msg, 'ok');
	} else {
		$sessio->msg('Could nor grant permissions on this package.', 'bad');
	}

	$kapenta->page->do302('code/showpackage/' . $_POST['packageUID']);

?>
