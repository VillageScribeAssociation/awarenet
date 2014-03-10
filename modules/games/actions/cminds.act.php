<?php

//--------------------------------------------------------------------------------------------------
//*	display the game swf
//--------------------------------------------------------------------------------------------------

	if ('banned' == $user->role) { $kapenta->page->do403(); }
	$kapenta->page->load('modules/games/actions/cminds.page.php');
	$kapenta->page->render();

?>
