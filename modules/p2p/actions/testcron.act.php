<?php

	require_once($kapenta->installPath . 'modules/p2p/inc/cron.inc.php');

//--------------------------------------------------------------------------------------------------
//|	temporary /development action to test p2p cron scripts
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	echo p2p_cron_tenmins();
	echo p2p_cron_daily();

?>
