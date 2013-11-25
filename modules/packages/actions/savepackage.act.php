<?

	require_once($kapenta->installPath . 'modules/packages/inc/kpackage.class.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to local copy of package manfiest or repository credentials
//--------------------------------------------------------------------------------------------------
//postarg: action - set to 'savePackage' [string]
//postarg: UID - UID of package to update [string]

	//----------------------------------------------------------------------------------------------
	//	check POST vars and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('savePackage' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not given.'); }

	$prefix = 'pkg.' . trim($_POST['UID']) . '.';

	$package = new KPackage($_POST['UID']);
	if (false == $package->loaded) { $page->do404('Could not load package.'); }

	//----------------------------------------------------------------------------------------------
	//	make the changes
	//----------------------------------------------------------------------------------------------
	foreach($_POST as $key => $value) {
		switch($key) {
			case 'username':	
				if (('' != trim($value)) && ($value != $package->username)) { 
					$kapenta->registry->set($prefix . 'user', $value); 
				}
				break;	//..........................................................................

			case 'password':	
				if (('' != trim($value)) && ($value != $package->password)) { 
					$kapenta->registry->set($prefix . 'pass', $value);
				}
				break;	//..........................................................................

			case 'includes':
				$package->includes = array();
				$lines = explode("\n", $value);
				foreach($lines as $line) { 
					$line = trim($line);
					if ('' != $line) { $package->includes[] = $line; }
				}
				$package->saveXml($package->fileName);
				break;	//..........................................................................

			case 'excludes':
				$package->excludes = array();
				$lines = explode("\n", $value);
				foreach($lines as $line) {
					$line = trim($line);
					if ('' != $line) { $package->excludes[] = $line; }
				}
				$package->saveXml($package->fileName);
				break;	//..........................................................................

			case 'installFile':
				$package->installFile = $_POST['installFile'];
				$package->saveXml($package->fileName);
				break;	//..........................................................................

			case 'installFn':
				$package->installFn = $_POST['installFn'];
				$package->saveXml($package->fileName);
				break;	//..........................................................................

		}
	}

	//----------------------------------------------------------------------------------------------
	//	try update the repository
	//----------------------------------------------------------------------------------------------

	$check = $package->postToRepository();
	if (true == $check) {
		$manifestLink = "<a href='" . $package->manifestUrl . "'>" . $package->manifestUrl . "</a>";
		$session->msg('Package updated on repository:<br/>' . $manifestLink, 'ok');

	} else {
		$msg = 'Could not update package metadata on repository, '
			 . 'please check credentials and network connection.';

		$session->msg($msg, 'warn');
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to package
	//----------------------------------------------------------------------------------------------
	$page->do302('packages/show/' . $package->UID);

?>
