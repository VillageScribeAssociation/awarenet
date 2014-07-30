<?

	require_once($kapenta->installPath . 'modules/chat/inc/hashes.class.php');
	require_once($kapenta->installPath . 'modules/chat/models/peers.set.php');
	require_once($kapenta->installPath . 'modules/chat/models/rooms.set.php');

//--------------------------------------------------------------------------------------------------
//*	test / development action to force generation of hashes
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	$hashes = new Chat_Hashes();
	$peers = new Chat_Peers();
	$rooms = new Chat_Rooms();

	echo "hashes.class.php nk: " . $hashes->nk() . "<br/>\n";
	echo "peers.set.php nk: " . $peers->nk() . "<br/>\n";
	echo "<br/>\n";

	echo "hashes.class.php ra: " . $hashes->ra() . "<br/>\n";
	echo "rooms.set.php ra: " . $rooms->ra() . "<br/>\n";
?>
