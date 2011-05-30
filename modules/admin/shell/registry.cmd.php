<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	commands for interacting with the registry
//--------------------------------------------------------------------------------------------------

function admin_WebShell_registry($args) {
	global $kapenta, $user, $shell, $theme, $registry;
	$mode = 'list';			//%	operation [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists(0, $args)) {
		switch($args[0]) {
			case '-d':			$mode = 'delete';	break;
			case '-g':			$mode = 'get';		break;
			case '-h':			$mode = 'help';		break;
			case '-l':			$mode = 'list';		break;
			case '-s':			$mode = 'set';		break;
			case '--delete':	$mode = 'delete';	break;
			case '--get':		$mode = 'get';		break;
			case '--help':		$mode = 'help';		break;
			case '--list':		$mode = 'list';		break;
			case '--set':		$mode = 'set';		break;
		}
	}

	if ('admin' != $user->role) { $mode = 'noauth'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'delete':
			if (false == array_key_exists(1, $args)) 
				{ return "<span class='ajaxwarn'>Key not given.</span>"; }

			$key = trim($args[1]);

			if (false == $registry->has($key)) 
				{ return "<span class='ajaxwarn'>Unknown key.</span>"; }

			$result = $registry->delete($key);

			if (true == $result) {
				$html .= "Deleted registry key <tt>$key</tt>";
			} else {
				$html .= "<span class='ajaxwarn'>Could not delete key <tt>$key</tt></span>";
			}
			break;	//..............................................................................

		case 'get':
			if (false == array_key_exists(1, $args)) 
				{ return "<span class='ajaxwarn'>Key not given.</span>"; }

			$key = trim($args[1]);

			if (false == $registry->has($key)) 
				{ return "<span class='ajaxwarn'>Unknown key.</span>"; }

			$value = $registry->get($key);

			$html .= "<tt>" . htmlentities($key) . " := " . htmlentities($value) . "</tt>";
			break;	//..............................................................................

		case 'help':
			$html = admin_WebShell_registry_help();
			break;	//..............................................................................

		case 'list':
			$keys = array();
			foreach($registry->keys as $key => $value) { $keys[] = $key; }
			sort($keys);
			foreach($keys as $key) { $html .= "<tt>" . $key . "</tt><br/>\n"; }
			break;	//..............................................................................

		case 'noauth':
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

		case 'set':
			if (false == array_key_exists(1, $args)) { 
				return "<span class='ajaxwarn'>Key not given.</span>"; 
			}

			if (false == array_key_exists(2, $args)) {
				return "<span class=''>Value not given.</span>";
			}

			$key = trim($args[1]);
			$value = trim($args[2]);
			$registry->set($key, $value);
			$html = htmlentities($key) . " := " . htmlentities($value);
			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the admin.registry command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function admin_WebShell_registry_help($short = false) {
	if (true == $short) { return "View, list and change settings."; }

	$html = "
	<b>usage: admin.registry [-d|-g|-h|-l|-s] [<i>key</i>][<i>value</i>]</b><br/>
	<br/>
	<b>[--delete|-d] <i>key</i></b><br/>
	Delete a registry key.<br/>
	<b>[--get|-g] <i>key</i></b><br/>
	Get the value of a registry key.<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<b>[--list|-l]</b><br/>
	Lists all registry keys.<br/>
	<b>[--set|-s] <i>key</i> <i>value</i></b><br/>
	Set the value of a registry key.<br/>
	<br/>
	";

	return $html;
}


?>
