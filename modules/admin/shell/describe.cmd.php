<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	describe a database table
//--------------------------------------------------------------------------------------------------

function admin_WebShell_describe($args) {
	global $kapenta;
	global $user;
	global $shell;
	global $theme;
	global $utils;
	global $db;

	$mode = 'html';							//%	operation [string]
	$ajw = "<span class='ajaxwarn'>";		//%	tidy [string]
	$html = '';								//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists(0, $args)) {
		switch($args[0]) {
			case '-h':			$mode = 'html';		break;
			case '-w':			$mode = 'wikicode';	break;
			case '--html':		$mode = 'html';		break;
			case '--wikicode':	$mode = 'wikicode';	break;
		}
	}

	if ('admin' != $user->role) { $mode = 'noauth'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'html':
			//--------------------------------------------------------------------------------------
			//	display table schema as HTML
			//--------------------------------------------------------------------------------------
			$tableName = '';
			if (true == array_key_exists(0, $args)) { $tableName = $args[0]; }
			if (true == array_key_exists(1, $args)) { $tableName = $args[1]; }
			if ('' == $tableName) { return $ajw . "Table name not given.</span>"; }
			$tableName = str_replace(';', '', trim($tableName));
			if (false == $db->tableExists($tableName)) { return $ajw . "No such table.</span>"; }

			$dba = new KDBAdminDriver();

			$dbSchema = $db->getSchema($tableName);
			$html .= $dba->schemaToHtml($dbSchema) . "<hr/>\n";

			break;	//..............................................................................

		case 'wikicode':
			//--------------------------------------------------------------------------------------
			//	display table schema as wikicode
			//--------------------------------------------------------------------------------------
			if (false == array_key_exists(1, $args)) { return $ajw . "Table name not given.</span>"; }
			$tableName = trim($args[1]);
			if (false == $db->tableExists($tableName)) { return $ajw . "No such table.</span>"; }

			$dbSchema = $db->getSchema($tableName);

			$html .= "<small><pre>";
			$html .= "|*| Field\t|| Type\t|| Index\t|| Comment\t||\n";
			foreach($dbSchema['fields'] as $name => $type) {
				$html .= "|| " . $name . "\t|| " . $type . "\t||\t||\t||\n\n";
			}
			$html .= "</pre></small>";

			break;	//..............................................................................

		case 'noauth':
			//--------------------------------------------------------------------------------------
			//	user not authorized
			//--------------------------------------------------------------------------------------
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the admin.describe command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function admin_WebShell_describe_help($short = false) {
	if (true == $short) { return "Print DB table schema."; }

	$html = "
	<b>usage: admin.describe [-h|-w] <i>tablename</i></b><br/>
	<br/>
	<b>[--html|-h] <i>tablename</i></b><br/>
	Display as HTML.<br/>
	<b>[--wikicode|-w] </b><br/>
	Print Kapenta wikicode.<br/>
	<br/>
	";

	return $html;
}


?>
