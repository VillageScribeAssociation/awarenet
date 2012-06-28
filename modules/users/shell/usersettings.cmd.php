<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');
	require_once($kapenta->installPath . 'modules/users/models/registry.mod.php');

//--------------------------------------------------------------------------------------------------
//|	shell interface to the database wrapper
//--------------------------------------------------------------------------------------------------

function users_WebShell_usersettings($args) {
	global $kapenta;
	global $user;
	global $shell;
	global $theme;
	global $db;

	$mode = 'list';			//%	operation [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists(0, $args)) {
		switch($args[0]) {
			case '-d':			$mode = 'del';		break;
			case '--delete':	$mode = 'del';		break;
			case '-g':			$mode = 'get';		break;
			case '--get':		$mode = 'get';		break;
			case '-h':			$mode = 'help';		break;
			case '--help':		$mode = 'help';		break;
			case '-l':			$mode = 'list';		break;
			case '--list':		$mode = 'list';		break;
			case '-s':			$mode = 'set';		break;
			case '--set':		$mode = 'set';		break;
		}
	}

	if ('admin' != $user->role) { $mode = 'noauth'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'list':
			if (false == array_key_exists(0, $args)) { return users_WebShell_usersettings_help(); }

			//--------------------------------------------------------------------------------------
			//	check arguments
			//--------------------------------------------------------------------------------------
			$raUID = '';
			$filter = '';
			$ajw = 	"<span class='ajaxwarn'>";

			if (true == array_key_exists(1, $args)) { $raUID = $args[1]; }
			if (true == array_key_exists(2, $args)) { $filter = $args[2]; }

			if (('-l' != $args[0]) && ('--list' != $args[0])) {
				$filter = $raUID;
				$raUID = $args[0];
			}

			if ('' == $raUID) { return $ajw . "User alias or UID not given.</span>"; }

			//--------------------------------------------------------------------------------------
			//	load user registry
			//--------------------------------------------------------------------------------------
			$model = new Users_User($raUID);
			if (false == $model->loaded) { return $ajw . "User not found.</span>"; }
			if ('' == $model->settings) { return "<span class='ajaxmsg'>Empty registry.</span>"; }
			if (false == $model->loadRegistry()) { return $ajw . "Error loading registry.</span>"; }

			$settings = $model->registry->toArray();
		
			//--------------------------------------------------------------------------------------
			//	display matching keys
			//--------------------------------------------------------------------------------------
			$table = array(array('Key', 'Value'));
			$filter = trim(strtolower($filter));

			foreach($settings as $key => $value) {
				if ('' == $filter) { $table[] = array($key, htmlentities($value)); }
				else {
					if (false !== strpos(strtolower($key), $filter)) {
						$table[] = array($key, htmlentities($value));
					}
				}
			}

			$html .= $theme->arrayToHtmlTable($table, true, true);
		
			break;	//..............................................................................

		case 'set':
			if (false == array_key_exists(0, $args)) { return users_WebShell_usersettings_help(); }

			//--------------------------------------------------------------------------------------
			//	check arguments
			//--------------------------------------------------------------------------------------
			$raUID = $args[0];
			$key = '';
			$value = '';
			$ajw = 	"<span class='ajaxwarn'>";

			if (true == array_key_exists(1, $args)) { $raUID = $args[1]; }
			if (true == array_key_exists(2, $args)) { $key = $args[2]; }
			if (true == array_key_exists(3, $args)) { $value = $args[3]; }

			if ('' == $raUID) { return $ajw . "User alias or UID not given.</span>"; }
			if ('' == $key) { return $ajw . "User registry key not given.</span>"; }
			if ('' == $value) { return $ajw . "User registry value not given.</span>"; }

			//--------------------------------------------------------------------------------------
			//	load user registry
			//--------------------------------------------------------------------------------------
			$model = new Users_User($raUID);
			if (false == $model->loaded) { return $ajw . "User not found.</span>"; }
			if ('' == $model->settings) { return "<span class='ajaxmsg'>Empty registry.</span>"; }
			if (false == $model->loadRegistry()) { return $ajw . "Error loading registry.</span>"; }

			//--------------------------------------------------------------------------------------
			//	set the key
			//--------------------------------------------------------------------------------------
			$check = $model->set($key, $value);
			if (true == $check) { $html .= "<pre>$key := " . htmlentities($value) . "</pre>"; }
			else { $html .= "<span class='ajaxerror'>Could not set user registry key.</span>"; }
			
			break;	//..............................................................................			

		case 'del':
			if (false == array_key_exists(0, $args)) { return users_WebShell_usersettings_help(); }

			//--------------------------------------------------------------------------------------
			//	check arguments
			//--------------------------------------------------------------------------------------
			$raUID = $args[0];
			$key = '';
			$value = '';
			$ajw = 	"<span class='ajaxwarn'>";

			if (true == array_key_exists(1, $args)) { $raUID = $args[1]; }
			if (true == array_key_exists(2, $args)) { $key = $args[2]; }

			if ('' == $raUID) { return $ajw . "User alias or UID not given.</span>"; }
			if ('' == $key) { return $ajw . "User registry key not given.</span>"; }

			//--------------------------------------------------------------------------------------
			//	load user registry
			//--------------------------------------------------------------------------------------
			$model = new Users_User($raUID);
			if (false == $model->loaded) { return $ajw . "User not found ($raUID).</span>"; }
			if ('' == $model->settings) { return "<span class='ajaxmsg'>Empty registry.</span>"; }
			if (false == $model->loadRegistry()) { return $ajw . "Error loading registry.</span>"; }

			//--------------------------------------------------------------------------------------
			//	delete the key
			//--------------------------------------------------------------------------------------
			if ('' == $model->get($key)) { return $ajw . "Key not found.</span>"; }

			$check = $model->set($key, '');
			if (true == $check) { $html .= "<span class='ajaxmsg'>Deleted key $key.</span>"; }
			else { $html .= "<span class='ajaxerror'>Could not delete user registry key.</span>"; }
		
			break;	//..............................................................................

		case 'get':
			$html .= "<span class='ajaxwarn'>Unimplemented.</span>";
			break;	//..............................................................................

		case 'help':
			$html = users_WebShell_usersettings_help();
			break;

		case 'noauth':
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the live.aliases command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function users_WebShell_usersettings_help($short = false) {
	if (true == $short) { return "Display and edit per-user registry settings."; }

	$html = "
	<b>usage: users.usersettings [-l|-g|-h|-s] <i>aliasOrUID</i> [key] [value]</b><br/>
	Display and modify user settings.<br/>
	<br/>
	<b>[--list|-l] <i>aliasOrUID {filter}</i></b><br/>
	Lists settings for the given user.<br/>
	<br/>
	<b>[--set|-s] <i>aliasOrUID</i> key value</b><br/>
	Set/create a user registry key.<br/>
	<br/>
	<b>[--delete|-f] <i>aliasOrUID</i> key</b><br/>
	Delete a user registry key.<br/>
	<br/>
	";

	return $html;
}


?>
