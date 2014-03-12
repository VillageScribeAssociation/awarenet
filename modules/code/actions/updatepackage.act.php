<?

	require_once($kapenta->installPath . 'modules/code/models/package.mod.php');
	require_once($kapenta->installPath . 'modules/code/inc/authenticate.inc.php');

//--------------------------------------------------------------------------------------------------
//*	action called by package manager on development machines
//--------------------------------------------------------------------------------------------------
//postarg: username - name of a kapenta user (ref:Users_User) [string]
//postarg: password - password of kapenta user (ref:Users_User) [string]
//postarg: package - UID of package to authenticate on (ref:Code_Package) [string]
//postarg: privilege - privilege requested [string]
//postopt: mode - set to 'basic' for now (only option) [string]
//postopt: return - set to 'xml', only option at present [string]
	
//TODO: add a better authentication system

	$mode = 'basic';
	$return = 'xml';
	$username = '';
	$privilege = 'commit';
	$packageUID = '';
	$password = '';

	//echo "<fail>package update aborted at start</fail>\n";
	//die();

	//----------------------------------------------------------------------------------------------
	//	check POST vars
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('username', $_POST)) { $username = $_POST['username']; }
	if (true == array_key_exists('password', $_POST)) { $password = $_POST['password']; }
	if (true == array_key_exists('privilege', $_POST)) { $privilege = $_POST['privilege']; }
	if (true == array_key_exists('packageUID', $_POST)) { $packageUID = $_POST['packageUID']; }

	if (('' == $username) || ('' == $packageUID) || ('' == $password)) { echo '<fail/>'; die(); }

	$model = new Code_Package($packageUID);
	if (false == $model->loaded) { echo '<fail>Unknown Package.</fail>'; die(); }

	//----------------------------------------------------------------------------------------------
	//	try authenticate the user
	//----------------------------------------------------------------------------------------------
	if (false == $kapenta->user->loadByName($username)) { echo '<fail>Unknown User.</fail>'; die(); }
	if (false == $kapenta->user->checkPassword($password)) { echo '<fail>Wrong password.</fail>'; die(); }

	if (false == $model->hasPrivilege($kapenta->user->UID, $privilege)) { 
		echo "<fail>No commit permission on this object.</fail>"; die(); 
	}

	//----------------------------------------------------------------------------------------------
	//	update the package
	//----------------------------------------------------------------------------------------------
	//TODO: input sanitizing here
	foreach($_POST as $key => $value) {
		switch($key) {
			case 'includes':	$model->includes = $value;			break;
			case 'excludes':	$model->excludes = $value;			break;			
			case 'installFile':	$model->installFile = $value;		break;
			case 'installFn':	$model->installFn = $value;			break;
		}
	}

	$model->revision = (int)$model->revision + 1;

//	echo "<fail>And I began to talk to myself almost immediately.</fail>";
//	die();

	$report = $model->save();
	if ('' == $report) {
		echo '<ok/>';
	} else {
		echo '<fail>Could not update package.</fail>'; die();
	}

	die();

?>
