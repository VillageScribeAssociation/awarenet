<?php

//--------------------------------------------------------------------------------------------------
//*	test broadcast event
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	$detail = array();
	$detail['message'] = ''
	 . "  <test>\n"
	 . "    <message>This is a test message.</message>\n"
	 . "  </test>\n";

	$kapenta->raiseEvent('p2p', 'p2p_broadcast', $detail);

	print_r($detail);

?>
