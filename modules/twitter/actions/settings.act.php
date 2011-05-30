<?

	require_once($kapenta->installPath . 'modules/twitter/inc/twitteroauth.class.php');
	require_once($kapenta->installPath . 'modules/twitter/inc/send.inc.php');

//--------------------------------------------------------------------------------------------------
//*	show page to edit twitter settigns
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	handle any POST vars
	//----------------------------------------------------------------------------------------------

	foreach($_POST as $key => $value) {
		switch($key) {

			case 'twitter_consumerkey':	
				$registry->set('twitter.consumerkey', $value);	
				break;	//..........................................................................

			case 'twitter_consumersecret':	
				$registry->set('twitter.consumersecret', $value);	
				break;	//..........................................................................

			case 'twitter_requesttoken':	
				$registry->set('twitter.requesttoken', $value);	
				break;	//..........................................................................

			case 'twitter_requesttokensecret':	
				$registry->set('twitter.requesttokensecret', $value);	
				break;	//..........................................................................

			case 'twitter_pin':	
				$registry->set('twitter.pin', $value);	
				break;	//..........................................................................

			case 'twitter_accesstoken':	
				$registry->set('twitter.accesstoken', $value);	
				break;	//..........................................................................

			case 'twitter_accesstokensecret':	
				$registry->set('twitter.accesstokensecret', $value);	
				break;	//..........................................................................

		}
	}

	//----------------------------------------------------------------------------------------------
	//	register application
	//----------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('register' == $_POST['action'])) {
		$consumerKey = $registry->get('twitter.consumerkey');
		$consumerSecret = $registry->get('twitter.consumersecret');
		
		if (('' == $consumerKey) || ('' == $consumerSecret)) {
			$session->msg('Please complete the consumer key and secret first.', 'bad');

		} else {
			$oauth = new TwitterOAuth($consumerKey, $consumerSecret);

			$request = $oauth->getRequestToken();
			$requestToken = $request['oauth_token'];
			$requestTokenSecret = $request['oauth_token_secret'];

			// store the generated request token/secret in the registry
			$registry->set('twitter.requesttoken', $requestToken);
			$registry->set('twitter.requesttokensecret', $requestTokenSecret);
	
			// display Twitter generated registration URL
			$registerURL = $oauth->getAuthorizeURL($request);			
			$msg = "<b>Important:</b> follow this link to get your PIN:<br/>"
				. "<a href='" . $registerURL . "' target='twn'>Register with Twitter</a><br/>"
				. "(You should be logged in to your twitter account before clicking it).";
			$session->msg($msg, 'ok');
		}
	}

	//----------------------------------------------------------------------------------------------
	//	validate PIN
	//----------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('validate' == $_POST['action'])) {
		$ok = true;

		// Retrieve our previously generated request token & secret
		$requestToken = $registry->get('twitter.requesttoken');
		$requestTokenSecret = $registry->get('twitter.requesttokensecret');

		$consumerKey = $registry->get('twitter.consumerkey');
		$consumerSecret = $registry->get('twitter.consumersecret');

		$PIN = '';
		if (true == array_key_exists('twitter_pin', $_POST)) { $PIN = $_POST['twitter_pin']; }

		if (('' == trim($consumerKey)) || ('' == trim($consumerSecret))) {
			$session->msg('Please complete consumer key and secret');
			$ok = false;
		}

		if (('' == trim($requestToken)) || ('' == trim($requestTokenSecret))) {
			$session->msg('Please complete request token and secret');
			$ok = false;
		}

		if ('' == trim($PIN)) {
			$session->msg('Please complete request token and secret');
			$ok = false;
		}

		if (true == $ok) {
			// create object passing request token/secret also
			$oauth = new TwitterOAuth(
				$consumerKey, $consumerSecret, 
				$requestToken, $requestTokenSecret
			);

			// Generate access token by providing PIN for Twitter
			//$request = $oauth->getAccessToken(NULL, $_GET["pin"]);
			$request = $oauth->getAccessToken(NULL, $PIN);
			$accessToken = $request['oauth_token'];
			$accessTokenSecret = $request['oauth_token_secret'];

			// Save our access token/secret to the registry
			$registry->set('twitter.accesstoken', $accessToken);
			$registry->set('twitter.accesstokensecret', $accessTokenSecret);

			$msg = "Stored Access Token and Secret... (PIN: $PIN)<br/>"
				. "Access Token: $accessToken <br/>"
				. "Access Token Secret: $accessTokenSecret <br/>";

			$session->msg($msg, 'ok');
		}
	}

	//----------------------------------------------------------------------------------------------
	//	test settings
	//----------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('test' == $_POST['action'])) {
		$tweet = '';
		if (true == array_key_exists('tweet', $_POST)) { 
			$tweet = substr(htmlentities($_POST['tweet']), 0, 140); 
		}	

		$report = twitter_send($tweet);
		$icon = 'ok';
		if (false != strpos($report, '<fail/>')) { $icon = 'bad'; }
		$session->msg($report, $icon);
	}

	//----------------------------------------------------------------------------------------------
	//	show the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/twitter/actions/settings.page.php');
	$page->render();

?>
