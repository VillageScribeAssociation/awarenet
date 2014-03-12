<?

//-------------------------------------------------------------------------------------------------
//|	generate authorization for channel subscriptions
//-------------------------------------------------------------------------------------------------
//arg: channel - id of channel [string]

function comments_channelauth($args) {
	global $kapenta;
	if (array_key_exists('channel', $args) == false) { return ''; }
	// TODO: check with module which owns comments
	if ($kapenta->user->role != 'public') { return 'yes'; }
}

?>

