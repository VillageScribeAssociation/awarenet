<?php

//-------------------------------------------------------------------------------------------------
//	sets editedBy and editedOn fields of an object to current user and time
//-------------------------------------------------------------------------------------------------

function live_WebShell_touch($args) {
	global $kapenta;
	global $kapenta;
	global $shell;
	global $kapenta;

	$mode = 'touch';		//%	operation [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == in_array('--help', $args)) { $mode = 'help'; }
	if (true == in_array('-h', $args)) { $mode = 'help'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------

	if (false == isset($shell)) { $shell = new Live_ShellSession(); }
	
	switch($mode) {
		case 'touch':
			$ok = true;

			if (
				(false == array_key_exists(0, $args)) ||
				(false == array_key_exists(1, $args))
			 ) { $ok = false; }

			if (true ==  $ok) { $model = $args[0]; $UID = $args[1]; }

			if ((true == $ok) && (false == $kapenta->db->objectExists($model, $UID))) {
				$html .= "<i>Object not found.</i><br/>";
				$ok = false;
			}

			if (true == $ok) {
					$dbSchema = $kapenta->db->getSchema($model);

					$objAry = $kapenta->db->getObject($model, $UID);
					$html.= "Model: $model UID: $UID<br/>\n";

					$objAry['editedOn'] = $kapenta->db->datetime();
					$objAry['editedBy'] = $kapenta->user->UID;
					
					$kapenta->db->save($objAry, $dbSchema);
			}
	
			if (false == $ok) {
				$html .= "<b>Usage: model / UID</b>";
			}

			break;	//..............................................................................

		case 'help':
			$html = live_WebShell_ls_help();
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

function live_WebShell_touch_help($short = false) {
	if (true == $short) { return "Touch an object."; }

	$html = "
	<b>usage: live.touch <i>model</i> <i>UID</i></b><br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	";

	return $html;
}


?>
