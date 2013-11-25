<?php

	require_once($kapenta->installPath . 'modules/live/inc/cmdinterpreter.class.php');
	require_once($kapenta->installPath . 'modules/live/inc/shellsession.class.php');

//--------------------------------------------------------------------------------------------------
//*	fired when another peer requests a webshell request
//--------------------------------------------------------------------------------------------------
//arg: session - UID of remote webshell session [string]
//arg: uid - unique ID of this request [string]
//arg: for - UID of a P2P_Peer object, or own p2p server [string]
//arg: from - UID of a trusted P2P_Peer object [string]
//arg: cmd64 - base64 encoded command to be executed [string]

function live__cb_p2p_webshell_received($args) {
	global $kapenta;
	global $user;
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('session', $args)) { return false; }
	if (false == array_key_exists('uid', $args)) { return false; }
	if (false == array_key_exists('for', $args)) { return false; }
	if (false == array_key_exists('from', $args)) { return false; }
	if (false == array_key_exists('cmd64', $args)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	initialize shell and run the command
	//----------------------------------------------------------------------------------------------
	$backupRole = $user->role;			//	event may be handled in 'public' or any user scope
	$user->role = 'admin';				//	grant admin permissions for this only

	$raw = base64_decode($args['cmd64']);

	$shell = new Live_ShellSession();
	$interpreter = new Live_CmdInterpreter($raw);

	$result = $kapenta->shellExecCmd($interpreter->cmd, $interpreter->arguments);

	$result = str_replace('%%serverPath%%', $kapenta->serverPath, $result);
	$result = str_replace('%%defaultTheme%%', $kapenta->defaultTheme, $result);

	$user->role = $backupRole;			//	restore previous role

	//----------------------------------------------------------------------------------------------
	//	return the result to the requesting peer
	//----------------------------------------------------------------------------------------------

	$message = ''
	 . "  <webshellresult>\n"
	 . "    <session>" . $args['session'] . "</session>"
	 . "    <uid>" . $args['uid'] . "</uid>"
	 . "    <for>" . $args['from'] . "</for>"
	 . "    <from>" . $kapenta->registry->get('p2p.server.uid') . "</from>"
	 . "    <cmd64>" . $args['cmd64'] . "</cmd64>"
	 . "    <result64>" . base64_encode($result) . "</result64>\n"
	 . "  </webshellresult>\n";

	$detail = array(
		'peer' => $args['from'],							//	send it back
		'message' => $message,
		'priority' => '2'									//	with high priority
	);

	$kapenta->raiseEvent('p2p', 'p2p_narrowcast', $detail);
}

?>
