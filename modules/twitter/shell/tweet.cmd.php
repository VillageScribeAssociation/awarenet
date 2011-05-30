<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');
	require_once($kapenta->installPath . 'modules/twitter/inc/twitteroauth.class.php');

//--------------------------------------------------------------------------------------------------
//|	simple utility to caclulate and return a SHA1 hash
//--------------------------------------------------------------------------------------------------

function twitter_WebShell_tweet($args) {
	global $kapenta, $user, $shell;
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

			// Read in our saved access token/secret
			$accessToken = $registry->get('twitter.accesstoken');
			$accessTokenSecret = $registry->get('twitter.accesstokensecret');

			$consumerKey = $registry->get('twitter.consumerkey');
			$consumerSecret = $registry->get('twitter.consumersecret');

			if (('' == trim($consumerKey)) || ('' == trim($consumerSecret))) {
				$html .= 'Please complete consumer key and secret<br/>';
				$ok = false;
			}

			if (('' == trim($accessToken)) || ('' == trim($accessTokenSecret))) {
				$html .= 'Please complete access token and secret<br/>';
				$ok = false;
			}

			if (true == $ok) {
				// Create our twitter API object	
				$oauth = new TwitterOAuth(
					$consumerKey, $consumerSecret, 
					$accessToken, $accessTokenSecret
				);

				// Send an API request to verify credentials
				$credentials = $oauth->get("account/verify_credentials");
				$html .= "Connected as @" . $credentials->screen_name;

				$tweet = 'awareNet: ' . implode(' ', $args);

				// Post our new "hello world" status
				$oauth->post('statuses/update', array('status' => $tweet));
			}

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
