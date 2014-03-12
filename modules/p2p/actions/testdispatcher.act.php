<?php

	require_once($kapenta->installPath . 'modules/p2p/inc/dispatcher.class.php');

//--------------------------------------------------------------------------------------------------
//*	action to test the p2p event dispatcher
//--------------------------------------------------------------------------------------------------
//ref: max number of events to displatch, default is 10 (int) [string]

	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }
	header("Content-type: text/plain");

	$maxEvents = 10;
	if ('' != $kapenta->request->ref) { $maxEvents = (int)$kapenta->request->ref; }

	$dispatcher = new P2P_Dispatcher();
	$pending = $dispatcher->listPending($maxEvents);

	if (0 == count($pending)) {
		echo "no outstanding events to be dispatched.\n";
		die();
	}

	print_r($pending);

	$dispatcher->dispatch($maxEvents);
?>
