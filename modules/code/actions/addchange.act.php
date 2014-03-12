<?

	require_once($kapenta->installPath . 'modules/code/models/package.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/change.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add a changelog entry (used by package manager on development clients)
//--------------------------------------------------------------------------------------------------

//postarg: username - name of a kapenta user (ref:Users_User) [string]
//postarg: password - password of kapenta user (ref:Users_User) [string]
//postarg: package - UID of package to authenticate on (ref:Code_Package) [string]
//postarg: privilege - privilege requested [string]
//postopt: mode - set to 'basic' for now (only option) [string]
//postopt: return - set to 'xml', only option at present [string]
	
//TODO: add a better authentication system, perhaps encrypt the request with server's public key

	$mode = 'basic';				//%	authentication method [string]
	$return = 'xml';				//%	response type [string]
	$username = '';					//%	repository user [string]
	$password = '';					//%	user's password [string]
	$privilege = 'commit';			//%	privilege needed [string]
	$packageUID = '';				//%	ref:Code_Package [string]
	$message = '';					//%	commit message [string]
	$files = '';					//%	list of files affected by change [string]

	//----------------------------------------------------------------------------------------------
	//	check POST vars
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('username', $_POST)) { $username = $_POST['username']; }
	if (true == array_key_exists('password', $_POST)) { $password = $_POST['password']; }
	if (true == array_key_exists('privilege', $_POST)) { $privilege = $_POST['privilege']; }
	if (true == array_key_exists('packageUID', $_POST)) { $packageUID = $_POST['packageUID']; }
	if (true == array_key_exists('message', $_POST)) { $message = $_POST['message']; }
	if (true == array_key_exists('files', $_POST)) { $files = $_POST['files']; }

	if (('' == $username) || ('' == $packageUID) || ('' == $password)) { echo '<fail/>'; die(); }

	if ('' == trim($message)) { echo '<fail>No message.</fail>'; die(); }
	if ('' == trim($files)) { echo '<fail>No files.</fail>'; die(); }

	$package = new Code_Package($packageUID);
	if (false == $package->loaded) { echo '<fail>Unknown Package.</fail>'; die(); }

	//----------------------------------------------------------------------------------------------
	//	try authenticate the user
	//----------------------------------------------------------------------------------------------
	if (false == $kapenta->user->loadByName($username)) { echo '<fail>Unknown User.</fail>'; die(); }
	if (false == $kapenta->user->checkPassword($password)) { echo '<fail>Wrong password.</fail>'; die(); }

	if (false == $package->hasPrivilege($kapenta->user->UID, $privilege)) { 
		echo "<fail>No commit permission on this object.</fail>"; die(); 
	}

	//----------------------------------------------------------------------------------------------
	//	add the changelog entry
	//----------------------------------------------------------------------------------------------
	$model = new Code_Change();
	$model->UID = $kapenta->createUID();
	$model->message = $message;
	$model->files = $files;
	$report = $model->save();

	if ('' == $report) {
		echo '<ok/>';
	} else {
		echo '<fail>Could not add changelog message.</fail>'; die();
	}

?>
