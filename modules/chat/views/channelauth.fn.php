<?

//-------------------------------------------------------------------------------------------------
//|	generate authorization for channel subscriptions
//-------------------------------------------------------------------------------------------------
//arg: channel - id of channel [string]

function chat_channelauth($args) {
	global $user;
	if (array_key_exists('channel', $args) == false) { return ''; }

	logSync('checking channel auth: ' . $args['channel'] . "\n");

	// NOTE: user may only subscribe to their own chat channel, may not eavesdrop on others
	if (substr($args['channel'], 0, 5) == 'user-') {
		$userUID = substr($args['channel'], 5);		
		if (($user->data['ofGroup'] != 'public') && ($user->data['UID'] == $userUID)) { 
			logSync('passed channel auth: ' . $args['channel'] . "\n");
			return 'yes'; 
		}
	}

	return '';
}

//-------------------------------------------------------------------------------------------------

?>

