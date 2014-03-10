<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	find common friends between the current user and some random, returns thumbnails
//--------------------------------------------------------------------------------------------------
//arg: friendshipUID - UID of relationship record [string]

function users_removefriend($args) {
		global $kapenta;
		global $user;

	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('friendshipUID', $args)) { return ''; }
	if (false == $kapenta->db->objectExists('users_friendship', $args['friendshipUID'])) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$rmPath = '%%serverPath%%users/removefriend/' . $args['friendshipUID'];

	$html = "<form name='' method='POST' action='" . $rmPath . "'>
	<input type='hidden' name='action' value='removeFriend' />
	<input type='hidden' name='friendshipUID' value='" . $args['friendshipUID'] . "' />
	<input type='submit' value='Remove From Friends List'>
	</form>	";

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
