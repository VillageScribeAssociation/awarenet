<?

//--------------------------------------------------------------------------------------------------
//	for debugging the chat bots
//--------------------------------------------------------------------------------------------------

	include $installPath . 'modules/chat/inc/bots.inc.php';
	$msg = ''; $html = '';

	//----------------------------------------------------------------------------------------------
	//	check for submitted messages
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('msg', $_POST)) {
		$msg = $_POST['msg'];
		$result = chatBotsProcess($msg, '124289610010992581');
		$html .= "<h2>sender</h2><textarea rows='10' cols='40'>" . $result['sender'] . "</textarea>\n";
		$html .= "<h2>recipient</h2><textarea rows='10' cols='40'>" . $result['recipient'] . "</textarea>\n";
	}

	//----------------------------------------------------------------------------------------------
	//	form for sending a message
	//----------------------------------------------------------------------------------------------

	$html .= "<form name='sned' method='POST'>
<textarea name='msg' rows='5' cols='40'>$msg</textarea>
<br/><input type='submit' />
</form>";

	//----------------------------------------------------------------------------------------------
	//	render page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/chat/actions/testbot.page.php');
	$page->blockArgs['html'] = $html;
	$page->render();

?>
