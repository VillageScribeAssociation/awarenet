<?

//--------------------------------------------------------------------------------------------------
//*	code for interfacing with chatbots
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	get a list of bots installed on the system
//--------------------------------------------------------------------------------------------------
//returns: list of chat bots [array]

function chatBotsList() {
	global $kapenta;
	$retVal = array();
	$shellCmd = 'ls ' . $kapenta->installPath . 'modules/chat/bots/';
	$list = shell_exec($shellCmd);

	$lines = explode("\n", $list);
	foreach($lines as $line) {

		if ((strpos($line, ".") == false) && ("" != $line))
			{ $retVal[] = trim($line); 
			}
	}
	
	return $retVal;
}

//--------------------------------------------------------------------------------------------------
//	process a message
//--------------------------------------------------------------------------------------------------

function chatBotsProcess($msg, $recipient) {
	global $kapenta;
	$retVal = array();
	$retVal['sender'] = $msg;
	$retVal['recipient'] = $msg;
	
	//----------------------------------------------------------------------------------------------
	//	check for /cmd
	//----------------------------------------------------------------------------------------------
	$msg = trim($msg) . ' ';
	if (substr($msg, 0, 1) == '/') {
		$spacePos = strpos($msg, ' ');
		if ($spacePos != false) {
			$botList = chatBotsList();
			$botCmd = str_replace('/', '', substr($msg, 0, $spacePos));

			//--------------------------------------------------------------------------------------			
			//	help is a special command impemented by this interface
			//--------------------------------------------------------------------------------------

			if ('help' == $botCmd) {
				$retVal['recipient'] = '';
				$msg = trim(str_replace('/help ', '', $msg));
				$about = '';

				//----------------------------------------------------------------------------------
				//	check if a bot has been named, eg /help math cosine
				//----------------------------------------------------------------------------------
				if ($msg != '') {
					$spacePos = strpos($msg, ' ');
					if ($spacePos != false) {
						$about = trim(substr($msg, 0, $spacePos));
						$msg = trim(substr($msg, $spacePos));
					} else {
						$about = $msg;
					}
				}

				if (($about != '') && (in_array(strtolower($about), $botList) == true)) {
					//------------------------------------------------------------------------------
					//	a bot has been named, let its help() function handle this
					//------------------------------------------------------------------------------

					$includeFile = $kapenta->installPath . 'modules/chat/bots/'
								 . $about . '/' . $about . '.bot.php';

					include $includeFile;
					$fnName = 'chat_bot_' . $about . '_help';
					$retVal['sender'] = $fnName($msg);
					return $retVal;					

				} else {
					//------------------------------------------------------------------------------
					//	bot not named or name not recognised, list bots
					//------------------------------------------------------------------------------
					$retVal['sender'] = "<font color='black'>The following bots are "
									 . "installed on this system:<br/>"
									 . "<font color=red>/help</font><br/>";

					foreach($botList as $botName) 
						{	$retVal['sender'] .= "<font color='red'>/". $botName ."</font><br/>"; }

					$retVal['sender'] .= "Type /help [bot name] for more information.</font>";
					return $retVal;

				}
			}

			//--------------------------------------------------------------------------------------			
			//	other stuff is handled by bots
			//--------------------------------------------------------------------------------------
			if (in_array(strtolower($botCmd), $botList) == true) {
				
				$includeFile = $kapenta->installPath . 'modules/chat/bots/'
							 . $botCmd . '/' . $botCmd . '.bot.php';

				require_once($includeFile);

				$fnName = 'chat_bot_' . $botCmd . '_submit';
				$retVal = $fnName($msg, $recipient);

			} 

		}
	}
	
	return $retVal;
}

?>
