<?php

	if (version_compare(PHP_VERSION, '5.0.0') >= 0) {
		require_once($kapenta->installPath . 'modules/twitter/inc/twitteroauth.class.php');
	}

//--------------------------------------------------------------------------------------------------
//*	send a status update to twitter
//--------------------------------------------------------------------------------------------------
//arg: message - message to send
//return: html report [string]

function twitter_send($message) {
	global $kapenta;
	global $session;

	$report = '';						//%	return value [string]
	$ok = true;							//%	if settings test pass [bool]

	if ('yes' != $kapenta->registry->get('twitter.enabled')) { return '<fail/>'; }

	// Read in our saved access token/secret
	$accessToken = $kapenta->registry->get('twitter.accesstoken');
	$accessTokenSecret = $kapenta->registry->get('twitter.accesstokensecret');

	$consumerKey = $kapenta->registry->get('twitter.consumerkey');
	$consumerSecret = $kapenta->registry->get('twitter.consumersecret');

	if (('' == trim($consumerKey)) || ('' == trim($consumerSecret))) {
		$report .= "Please complete consumer key and secret.<br/>\n";
		$ok = false;
	}

	if (('' == trim($accessToken)) || ('' == trim($accessTokenSecret))) {
		$report .= "Please complete access token and secret.<br/>\n";
		$ok = false;
	}

	if ($message == $kapenta->registry->get('twitter.lasttweet')) {
		$report .= "message repetition";
		$ok = false;
	}

	if ('' == trim($message)) {
		$report .= "No tweet sent.<br/>\n";
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
		$report .= "Connected as @" . $credentials->screen_name . "<br/>\n";

		// Post our new "hello world" status
		$response = $oauth->post('statuses/update', array('status' => $message));
		if (true == is_array($message)) { $session->msgAdmin(implode($response)); }
		$report .= "Sending tweet: " . $message . "<br/>\n";
		$report .= "<ok/>\n";

		// record this tweet to prevent repetitions
		$kapenta->registry->set('twitter.lasttweet', $message);

	} else {
		$report .= "<fail/>\n";
	}

	return $report;
}

?>
