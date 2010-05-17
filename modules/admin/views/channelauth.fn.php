<?

//-------------------------------------------------------------------------------------------------
//|	
//-------------------------------------------------------------------------------------------------
//arg: channel - ID of channel on this module [string]

function admin_channelauth($args) {
	global $user;
	if (array_key_exists('channel', $args) == false) { return ''; }

	switch ($args['channel']) {
		case 'syspagelog': 			if ($user->data['ofGroup'] == 'admin') {return 'yes'; }
									break;

		case 'syspagelogsimple': 	if ($user->data['ofGroup'] == 'admin') {return 'yes'; }
									break;
	}
	return 'no';
}

//-------------------------------------------------------------------------------------------------
?>

