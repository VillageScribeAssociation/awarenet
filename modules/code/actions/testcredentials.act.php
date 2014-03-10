<?

	require_once($kapenta->installPath . 'modules/code/inc/authenticate.inc.php');

//--------------------------------------------------------------------------------------------------
//*	action for testing user credentials on repository
//--------------------------------------------------------------------------------------------------
//postarg: username - name of a kapenta user (ref:Users_User) [string]
//postarg: password - password of kapenta user (ref:Users_User) [string]
//postarg: package - UID of package to authenticate on (ref:Code_Package) [string]
//postarg: privilege - privilege requested [string]
//postopt: mode - set to 'basic' for now (only option) [string]
//postopt: return - set to 'xml', only option at present [string]

//TODO: add a better authentication system

	$mode = 'basic';
	$ret = 'xml';
	$username = '';
	$privilege = 'commit';
	$packageUID = '';
	$password = '';

	//----------------------------------------------------------------------------------------------
	//	check POST vars
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('username', $_POST)) { $username = $_POST['username']; }
	if (true == array_key_exists('password', $_POST)) { $password = $_POST['password']; }
	if (true == array_key_exists('privilege', $_POST)) { $privilege = $_POST['privilege']; }
	if (true == array_key_exists('packageUID', $_POST)) { $packageUID = $_POST['packageUID']; }

	if (('' == $username) || ('' == $packageUID) || ('' == $password)) { echo '<fail/>'; die(); }

	if (false == code_authenticate($username, $password, $packageUID, $privilege)) { 
		echo '<fail/>'; die(); 
	} else {
		echo "<ok/>";
	}

?>
