<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');
	require_once($kapenta->installPath . 'modules/packages/inc/ksource.class.php');
	require_once($kapenta->installPath . 'modules/packages/inc/kpackage.class.php');
	require_once($kapenta->installPath . 'modules/packages/inc/kupdatemanager.class.php');

//--------------------------------------------------------------------------------------------------
//|	lists packages installed on this kapenta instance
//--------------------------------------------------------------------------------------------------

function packages_WebShell_kpkg($args) {
	global $kapenta;
	global $user;
	global $shell;
	global $registry;

	$mode = 'list';			//%	operation [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	//if ('admin' != $user->role) { $mode = 'noauth'; }
	if (true == in_array('--help', $args)) { $mode = 'help'; }
	if (true == in_array('-h', $args)) { $mode = 'help'; }
	if (true == in_array('--fix', $args)) { $mode = 'fix'; }
	if (true == in_array('-f', $args)) { $mode = 'fix'; }

	//----------------------------------------------------------------------------------------------
	//	check if a package name was given
	//----------------------------------------------------------------------------------------------

	//TODO: this

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'list':
			
			break;	//..............................................................................

		case 'show':
			$html .= "TODO: this";
			break;	//..............................................................................

		case 'fix':
			//TODO: this should load and check manifests, and run the install status function

			$registry->load('pkg');
			$packages = array();			//%	all packages in registry [array:dict]
			$sources = array();				//%	all package sources [array:string]
			$installed = array();			//%	UIDs of installed packages [array:string]
			$table = array();				//%	[array:string]
	
			//--------------------------------------------------------------------------------------
			//	get packages fron registry
			//--------------------------------------------------------------------------------------
			foreach($registry->keys as $key => $value64) {
				if ('pkg' == $registry->getPrefix($key)) {
					$parts = explode('.', $key);
					if (3 == count($parts)) {
						if (false == array_key_exists($parts[1], $packages)) {
							$packages[$parts[1]] = array();
						}
						$packages[$parts[1]][$parts[2]] = $registry->get($key);
					}
				}
			}

			//--------------------------------------------------------------------------------------
			//	make packages list and set 'dirty' to force check
			//--------------------------------------------------------------------------------------
			foreach($packages as $UID => $pkg) {
				if (
					(true == array_key_exists('status', $pkg)) && 
					(true == array_key_exists('source', $pkg)) && 
					('installed' == $pkg['status']) &&
					('' != $pkg['source'])
				) {
										
					$registry->set('pkg.' . $UID . '.dirty', 'yes');
					$installed[] = $UID;
					if (false == in_array($pkg['source'], $sources)) { $sources[] = $pkg['source'];	}
					$html .= "package: $UID (installed from " . $pkg['source'] . ")<br/>";
				}
			}

			$registry->set('kapenta.sources.list', implode('|', $sources));
			break;	//..............................................................................

		case 'help':
			$html = packages_WebShell_kpkg_help();
			break;			

		case 'noauth':
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the admin.time command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function packages_WebShell_kpkg_help($short = false) {
	if (true == $short) { return "List packages installed on this system."; }

	$html = "
	<b>usage: pakages.kpkg [-s|--show] [UID|Name]</b><br/>
	Displays contents of an installed kapenta package.
	<br/>
	<b>[--fix|-f]</b><br/>
	Attempt to repair broken metadata and registry entries.<br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	";

	return $html;
}


?>
