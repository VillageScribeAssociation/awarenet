<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');
	require_once($kapenta->installPath . 'modules/twitter/inc/twitteroauth.class.php');
	require_once($kapenta->installPath . 'modules/twitter/inc/send.inc.php');

//--------------------------------------------------------------------------------------------------
//|	simple utility to caclulate and return a SHA1 hash
//--------------------------------------------------------------------------------------------------

function twitter_WebShell_tweet($args) {
	global $kapenta;
	global $kapenta;
	global $kapenta;
	global $shell;

	$html = '';								//%	return value [string]
	$mode = 'tweet';						//%	operation [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	foreach ($args as $idx => $arg) {
		switch($arg) {
			case '-h':			$mode = 'help';		break;
			case '-t':			$mode = 'tweet';	break;
			case '--help':		$mode = 'help';		break;
			case '--tweet':		$mode = 'tweet';	break;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'tweet':
			$ok = true;

			$tweet = 'awareNet: ' . implode(' ', $args);
			$html .= twitter_send($tweet);

			break;	//..............................................................................

		case 'help':
			$html = live_WebShell_sha1_help();
			break;			

		case 'noauth':
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the live.sha1 command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function twitter_WebShell_tweet_help($short = false) {
	if (true == $short) { return "Send a twitter update."; }

	$html = "
	<b>usage: twitter.tweet [-h|-t] [<i>\"tweet\"</i>]</b><br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	<b>[--tweet|-t] <i>value</i></b><br/>
	Send an update.<br/>
	<br/>
	";

	return $html;
}


?>
