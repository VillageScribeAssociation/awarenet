<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	commands for interacting with the registry
//--------------------------------------------------------------------------------------------------

function admin_WebShell_registry($args) {
	global $kapenta;
	global $user;
	global $shell;
	global $theme;
	global $kapenta;
	global $utils;

	$mode = 'list';							//%	operation [string]
	$html = '';								//%	return value [string]
	$ajw = "<span class='ajaxwarn'>";		//%	tidy [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists(0, $args)) {
		switch($args[0]) {
			case '-d':			$mode = 'delete';	break;
			case '-f':			$mode = 'files';	break;
			case '-g':			$mode = 'get';		break;
			case '-h':			$mode = 'help';		break;
			case '-l':			$mode = 'list';		break;
			case '-v':			$mode = 'value';	break;
			case '-s':			$mode = 'set';		break;
			case '--delete':	$mode = 'delete';	break;
			case '--files':		$mode = 'files';	break;
			case '--get':		$mode = 'get';		break;
			case '--help':		$mode = 'help';		break;
			case '--list':		$mode = 'list';		break;
			case '--set':		$mode = 'set';		break;
			case '--value':		$mode = 'value';	break;
		}
	}

	if ('admin' != $user->role) { $mode = 'noauth'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'delete':
			//--------------------------------------------------------------------------------------
			//	delete a registry key
			//--------------------------------------------------------------------------------------
			$kapenta->registry->loadAll();
			if (false == array_key_exists(1, $args)) { return $ajw . "Key not given.</span>"; }
			$key = trim($args[1]);
			if (false == $kapenta->registry->has($key)) { return $ajw . "Unknown key.</span>"; }
			$result = $kapenta->registry->delete($key);
			if (true == $result) { $html .= "Deleted registry key <tt>$key</tt>"; }
			else { $html .= $ajw . "Could not delete key <tt>$key</tt></span>"; }
			break;	//..............................................................................

		case 'files':
			//--------------------------------------------------------------------------------------
			//	make a list of all registry sections
			//--------------------------------------------------------------------------------------
			$files = $kapenta->registry->listFiles();
			foreach($files as $file) {
				$html .= "<tt>$file</tt><br/>";
			}
			break;	//..............................................................................

		case 'get':
			//--------------------------------------------------------------------------------------
			//	get the value of a registry key
			//--------------------------------------------------------------------------------------
			if (false == array_key_exists(1, $args)) { return $ajw . "Key not given.</span>"; }
			$key = trim($args[1]);
			if (false == $kapenta->registry->has($key)) { return $ajw . "Unknown key.</span>"; }
			$value = $kapenta->registry->get($key);

			$html .= ''
			 . "<tt>"
			 . htmlentities($key,ENT_QUOTES, "UTF-8")
			 . " := "
			 . htmlentities($value, ENT_QUOTES, "UTF-8")
			 . "</tt>";

			break;	//..............................................................................

		case 'help':
			//--------------------------------------------------------------------------------------
			//	display the manpage
			//--------------------------------------------------------------------------------------
			$html = admin_WebShell_registry_help();
			break;	//..............................................................................

		case 'list':
			//--------------------------------------------------------------------------------------
			//	list all keys with a given prefix
			//--------------------------------------------------------------------------------------
			$kapenta->registry->loadAll();	
			$prefix = '';
			if (true == array_key_exists(1, $args)) { $prefix = strtolower(trim($args[1])); }

			$keys = array();
			$prefixlen = strlen($prefix);
			foreach($kapenta->registry->keys as $key => $value) { 
				if ((substr($key, 0, $prefixlen) == $prefix) || ('' == $prefix)) { 
					$value = base64_decode($value);
					$value = $utils->cleanTitle($value);
					//if (strlen($value) > 40) { $value = substr($value, 0, 40) . "..."; }
					$keys[$key] = $value; 
				}
			}

			$table = array(array('key', 'value'));
			ksort($keys);
			foreach($keys as $k => $v) { $table[] = array("<tt>$k</tt>", "<tt>$v</tt>"); }
			$html .= $theme->arrayToHtmlTable($table, true, true);

			break;	//..............................................................................

		case 'noauth':
			//--------------------------------------------------------------------------------------
			//	user not authorized
			//--------------------------------------------------------------------------------------
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

		case 'set':
			//--------------------------------------------------------------------------------------
			//	set/change the value of a registry key
			//--------------------------------------------------------------------------------------
			if (false == array_key_exists(1, $args)) { return $ajw . "Key not given.</span>"; }
			if (false == array_key_exists(2, $args)) { return $ajw . "Value not given.</span>"; }

			echo $args[2] . "<br/>";

			$key = trim($args[1]);
			$value = trim($args[2]);
			$kapenta->registry->set($key, $value);

			$html = ''
				 . htmlentities($key, ENT_QUOTES, "UTF-8")
				 . " := "
				 . htmlentities($value, ENT_QUOTES, "UTF-8");

			break;	//..............................................................................

		case 'value':
			//--------------------------------------------------------------------------------------
			//	find a given value in the registry
			//--------------------------------------------------------------------------------------
			if (false == array_key_exists(1, $args)) { return $ajw . "Value not given.</span>"; }

			$find = mb_strtolower(trim($args[1]));

			$kapenta->registry->loadAll();	

			$keys = array();
			foreach($kapenta->registry->keys as $key => $value) {
				$value = base64_decode($value);
				if (
					(false !== mb_strpos(mb_strtolower($value), $find, 0, 'UTF-8')) ||
					(false !== mb_strpos(mb_strtolower($key), $find, 0, 'UTF-8')) 
				) {
					$value = $utils->cleanTitle($value);
					$keys[$key] = $value; 
				}
			}

			$table = array(array('key', 'value'));
			ksort($keys);
			foreach($keys as $k => $v) { $table[] = array("<tt>$k</tt>", "<tt>$v</tt>"); }
			$html .= $theme->arrayToHtmlTable($table, true, true);

			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the admin.registry command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function admin_WebShell_registry_help($short = false) {
	if (true == $short) { return "View, list and change settings in the registry."; }

	$html = "
	<b>usage: admin.registry [-d|-g|-h|-l|-s|-v] [<i>key</i>][<i>value</i>]</b><br/>
	<br/>
	<b>[--delete|-d] <i>key</i></b><br/>
	Delete a registry key.<br/>
	<b>[--files|-f] </b><br/>
	Lists all registry sections (files).<br/>
	<b>[--delete|-d] <i>key</i></b><br/>
	Delete a registry key.<br/>
	<b>[--get|-g] <i>key</i></b><br/>
	Get the value of a registry key.<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<b>[--list|-l] [<i>file</i>]</b><br/>
	Lists all registry keys in a given prefix.<br/>
	<b>[--set|-s] <i>key</i> <i>value</i></b><br/>
	Set the value of a registry key.<br/>
	<br/>
	<b>[--value|-v] <i>value</i></b><br/>
	Search for registry entries matching the given value.<br/>
	<br/>
	";

	return $html;
}


?>
