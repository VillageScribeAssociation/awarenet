<?php

//--------------------------------------------------------------------------------------------------
//*	test of modal windows with jQuery
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $kapenta->page->do403(); }

	$kapenta->page->load('modules/live/actions/testmodal.page.php');
	$kapenta->page->render();

?>
