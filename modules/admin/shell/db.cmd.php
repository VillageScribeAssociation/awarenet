<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	shell interface to the database wrapper
//--------------------------------------------------------------------------------------------------

function admin_WebShell_db($args) {
		global $kapenta;
		global $user;
		global $shell;
		global $theme;
		global $db;

	$mode = 'query';		//%	operation [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists(0, $args)) {
		switch($args[0]) {
			case '-h':			$mode = 'help';		break;
			case '--help':		$mode = 'help';		break;
		}
	}

	if ('admin' != $user->role) { $mode = 'noauth'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'query':
			if (false == array_key_exists(0, $args)) { return admin_WebShell_db_help(); }
			$sql = implode(' ', $args) . ';';
			//$html = "executing database query: " . implode(' ', $args);
			$result = $db->query($sql);
		
			if (false === $result) { return "Database returns FALSE"; }
			if (true === $result) { return "Database returns TRUE"; }

			$table = array();

			while ($row = $db->fetchAssoc($result)) {
				if (0 == count($table)) {
					$titles = array();
					foreach($row as $key => $value) {	$titles[] = $key; }
					$table[] = $titles;
				}

				$clean = array();
				foreach($row as $key => $value) {	
					$clean[] = "<pre>" . htmlentities($value, ENT_QUOTES, "UTF-8") . "</pre>"; 
				}
				
				$table[] = $clean;
			}

			$html .= $theme->arrayToHtmlTable($table, true, true);

			break;	//..............................................................................

		case 'help':
			$html = live_WebShell_cd_help();
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

function admin_WebShell_db_help($short = false) {
	if (true == $short) { return "Interface to the database wrapper."; }

	$html = "
	<b>usage: admin.db <i>query</i></b><br/>
	Executes a SQL query and returns results, if any.<br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	";

	return $html;
}


?>
