<?

	require_once($kapenta->installPath . 'modules/twitter/inc/twitteroauth.class.php');
	require_once($kapenta->installPath . 'modules/twitter/inc/send.inc.php');

//--------------------------------------------------------------------------------------------------
//*	show page to edit twitter settings
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	handle any POST vars
	//----------------------------------------------------------------------------------------------
	foreach($_POST as $key => $value) {
		switch($key) {

			case 'twitter_consumerkey':	
				$kapenta->registry->set('twitter.consumerkey', $value);	
				break;	//..........................................................................

			case 'twitter_consumersecret':	
				$kapenta->registry->set('twitter.consumersecret', $value);	
				break;	//..........................................................................

			case 'twitter_requesttoken':	
				$kapenta->registry->set('twitter.requesttoken', $value);	
				break;	//..........................................................................

			case 'twitter_requesttokensecret':	
				$kapenta->registry->set('twitter.requesttokensecret', $value);	
				break;	//..........................................................................

			case 'twitter_pin':	
				$kapenta->registry->set('twitter.pin', $value);	
				break;	//..........................................................................

			case 'twitter_accesstoken':	
				$kapenta->registry->set('twitter.accesstoken', $value);	
				break;	//..........................................................................

			case 'twitter_accesstokensecret':	
				$kapenta->registry->set('twitter.accesstokensecret', $value);	
				break;	//..........................................................................

		}
	}

	//----------------------------------------------------------------------------------------------
	//	register application
	//----------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('register' == $_POST['action'])) {
		$consumerKey = $kapenta->registry->get('twitter.consumerkey');
		$consumerSecret = $kapenta->registry->get('twitter.consumersecret');
		
		if (('' == $consumerKey) || ('' == $consumerSecret)) {
			$kapenta->session->msg('Please complete the consumer key and secret first.', 'bad');

		} else {
			$oauth = new TwitterOAuth($consumerKey, $consumerSecret);

			$request = $oauth->getRequestToken();
			$requestToken = $request['oauth_token'];
			$requestTokenSecret = $request['oauth_token_secret'];

			// store the generated request token/secret in the registry
			$kapenta->registry->set('twitter.requesttoken', $requestToken);
			$kapenta->registry->set('twitter.requesttokensecret', $requestTokenSecret);
	
			// display Twitter generated registration URL
			$registerURL = $oauth->getAuthorizeURL($request);			
			$msg = "<b>Important:</b> follow this link to get your PIN:<br/>"
				. "<a href='" . $registerURL . "' target='twn'>Register with Twitter</a><br/>"
				. "(You should be logged in to your twitter account before clicking it).";
			$kapenta->session->msg($msg, 'ok');
		}
	}

	//----------------------------------------------------------------------------------------------
	//	validate PIN
	//----------------------------------------------------------------------------------------------
	if ((true == array_key_exists('action', $_POST)) && ('validate' == $_POST['action'])) {
		$ok = true;

		// Retrieve our previously generated request token & secret
		$requestToken = $kapenta->registry->get('twitter.requesttoken');
		$requestTokenSecret = $kapenta->registry->get('twitter.requesttokensecret');

		$consumerKey = $kapenta->registry->get('twitter.consumerkey');
		$consumerSecret = $kapenta->registry->get('twitter.consumersecret');

		$PIN = '';
		if (true == array_key_exists('twitter_pin', $_POST)) { $PIN = $_POST['twitter_pin']; }

		if (('' == trim($consumerKey)) || ('' == trim($consumerSecret))) {
			$kapenta->session->msg('Please complete consumer key and secret');
			$ok = false;
		}

		if (('' == trim($requestToken)) || ('' == trim($requestTokenSecret))) {
			$kapenta->session->msg('Please complete request token and secret');
			$ok = false;
		}

		if ('' == trim($PIN)) {
			$kapenta->session->msg('Please complete request token and secret');
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
			$kapenta->registry->set('twitter.accesstoken', $accessToken);
			$kapenta->registry->set('twitter.accesstokensecret', $accessTokenSecret);

			$msg = "Stored Access Token and Secret... (PIN: $PIN)<br/>"
				. "Access Token: $accessToken <br/>"
				. "Access Token Secret: $accessTokenSecret <br/>";

			$kapenta->session->msg($msg, 'ok');
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
		$kapenta->session->msg($report, $icon);
	}

	//----------------------------------------------------------------------------------------------
	//	show the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/twitter/actions/settings.page.php');
	$kapenta->page->render();

?>
