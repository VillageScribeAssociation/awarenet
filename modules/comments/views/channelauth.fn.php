<?

//-------------------------------------------------------------------------------------------------
//|	generate authorization for channel subscriptions
//-------------------------------------------------------------------------------------------------
//arg: channel - id of channel [string]

function comments_channelauth($args) {
	global $user;
	if (array_key_exists('channel', $args) == false) { return ''; }
	// TODO: check with module which owns comments
	if ($user->data['ofGroup'] != 'public') { return 'yes'; }
}

?>

