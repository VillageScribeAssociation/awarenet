<?php

//--------------------------------------------------------------------------------------------------
//*	test broadcast event
//--------------------------------------------------------------------------------------------------

	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	$detail = array();
	$detail['message'] = ''
	 . "  <test>\n"
	 . "    <message>This is a test message.</message>\n"
	 . "  </test>\n";

	$kapenta->raiseEvent('p2p', 'p2p_broadcast', $detail);

	print_r($detail);

?>
