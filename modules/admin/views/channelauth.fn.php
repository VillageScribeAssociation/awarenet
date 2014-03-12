<?

//-------------------------------------------------------------------------------------------------
//|	
//-------------------------------------------------------------------------------------------------
//arg: channel - ID of channel on this module [string]

function admin_channelauth($args) {
	global $kapenta;
	if (array_key_exists('channel', $args) == false) { return ''; }

	switch ($args['channel']) {
		case 'syspagelog': 			if ('admin' == $kapenta->user->role) {return 'yes'; }
									break;

		case 'syspagelogsimple': 	if ('admin' == $kapenta->user->role) {return 'yes'; }
									break;
	}
	return 'no';
}

//-------------------------------------------------------------------------------------------------
?>

