<?

//--------------------------------------------------------------------------------------------------
//*	execute a web shell command
//--------------------------------------------------------------------------------------------------

	if ('admin' != $kapenta->user->role) { $kapenta->page->doXmlError('not authenticated.'); }

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('cmd', $_POST)) { die('cmd not given'); }
	if (false == array_key_exists('peer', $_POST)) { die('peer not given'); }
	if (false == array_key_exists('session', $_POST)) { die('session not given'); }

	$cmdUID = $kapenta->createUID();

	$message = ''
	 . "<webshell>\n"
	 . "  <uid>$cmdUID</uid>\n"
	 . "  <session>" . $_POST['session'] . "</session>\n"
	 . "  <from>" . $kapenta->registry->get('p2p.server.uid') . "</from>\n"
	 . "  <for>" .  $_POST['peer'] . "</for>\n"
	 . "  <cmd64>" . $_POST['cmd'] . "</cmd64>\n"
	 . "<webshell>\n";

	$detail = array(
		'peer' => $_POST['peer'],
		'message' => $message,
		'priority' => '2'
	);

	if ('*' == $_POST['peer']) {
		$kapenta->raiseEvent('p2p', 'p2p_broadcast', $detail);
	} else {
		//echo "raising narrowcast event:<br/>\n";
		$kapenta->raiseEvent('p2p', 'p2p_narrowcast', $detail);
	}

	//print_r($detail);	

	$raw = base64_decode($_POST['cmd']);


?>
