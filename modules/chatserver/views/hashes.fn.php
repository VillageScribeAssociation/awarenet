<?

	require_once($kapenta->installPath . 'modules/chatserver/models/peers.set.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/sessions.set.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/rooms.set.php');

//--------------------------------------------------------------------------------------------------
//|	display table of important hashes of current state
//--------------------------------------------------------------------------------------------------

function chatserver_hashes($args) {
	global $theme;
	global $user;

	//----------------------------------------------------------------------------------------------
	//	check user role and any arguments
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	// ^ add any arguments here

	$peers = new Chatserver_Peers();
	$rooms = new Chatserver_Rooms();
	
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$table = array();
	$table[] = array('Key', 'Hash');
	$table[] = array('nk', $peers->nk());
	$table[] = array('ra', $rooms->ra());
	
	$html = $theme->arrayToHtmlTable($table, true, true);

	return $html;
}

?>
