<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	commands for interacting with the registry
//--------------------------------------------------------------------------------------------------

function admin_WebShell_memcached($args) {
	global $kapenta;
	global $user;
	global $shell;
	global $theme;
	global $kapenta;
	global $utils;

	$available = class_exists('Memcached');		//%	Memcached module available [bool]
	$mode = 'status';							//%	operation [string]
	$html = '';									//%	return value [string]
	$ajw = "<span class='ajaxwarn'>";			//%	tidy [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists(0, $args)) {
		switch($args[0]) {
			case '-d':			$mode = 'delete';	break;
			case '-f':			$mode = 'flush';	break;
			case '-h':			$mode = 'help';		break;
			case '-s':			$mode = 'show';		break;
			case '-t':			$mode = 'status';	break;
			case '--delete':	$mode = 'delete';	break;
			case '--flush':		$mode = 'flush';	break;
			case '--help':		$mode = 'help';		break;
			case '--show':		$mode = 'show';		break;
			case '--status':	$mode = 'status';	break;
		}
	}

	if ('admin' != $user->role) { $mode = 'noauth'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {

		case 'delete':
			//--------------------------------------------------------------------------------------
			//	invalidate an item in the cache
			//--------------------------------------------------------------------------------------

			break;	//..............................................................................

		case 'flush':
			if (true == $available) {
				$html .= "All cache objects invalidated.";
				$kapenta->cacheFlush();
			} else {
				$html .= "Memcached object is <span class='ajaxwarn'>not available</span>";
			}
			//--------------------------------------------------------------------------------------
			//	invalidate all cached items
			//--------------------------------------------------------------------------------------
			
			break;	//..............................................................................

		case 'show':
			//--------------------------------------------------------------------------------------
			//	show the value of a cache key
			//--------------------------------------------------------------------------------------
			break;	//..............................................................................

		case 'help':
			//--------------------------------------------------------------------------------------
			//	display the manpage
			//--------------------------------------------------------------------------------------
			$html = admin_WebShell_memcached_help();
			break;	//..............................................................................

		case 'noauth':
			//--------------------------------------------------------------------------------------
			//	user not authorized
			//--------------------------------------------------------------------------------------
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

		case 'status':
			//--------------------------------------------------------------------------------------
			//	get status of memcached extension
			//--------------------------------------------------------------------------------------
			if (true == $available) {
				$html .= "Memcached object is <span class='ajaxmsg'>available</span>";

				$stats = $kapenta->mc->getStats();
				foreach($stats as $server => $kv) {
					$html .= "<b>Service: $server</b><br/>\n";
					$table = array(array('Metric', 'Value'));

					foreach($kv as $key => $value) {
						$table[] = array($key, $value);
					}
		
					$html .= $theme->arrayToHtmlTable($table, true, true);
				}

			} else {
				$html .= "Memcached object is <span class='ajaxwarn'>not available</span>";
			}
			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the admin.registry command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function admin_WebShell_memcached_help($short = false) {
	if (true == $short) { return "View and clear memcached objects."; }

	$html = "
	<b>usage: admin.memcached [-c|-d|-s|-t] [<i>key</i>]</b><br/>
	<br/>
	<b>[--clear|-c] </b><br/>
	Invalidate all memcached items.<br/>
	<b>[--delete|-d] <i>key</i></b><br/>
	Invalidate a cache entry.<br/>
	<b>[--show|-s] <i>key</i></b><br/>
	Show the memcached value of a key.<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<b>[--status|-t] <i>key</i> <i>value</i></b><br/>
	Show status of memcached PHP extension.<br/>
	<br/>
	";

	return $html;
}


?>
