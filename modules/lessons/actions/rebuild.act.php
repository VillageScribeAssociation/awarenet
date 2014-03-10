<?php

	require_once($kapenta->installPath . 'modules/lessons/inc/install.inc.php');
	require_once($kapenta->installPath . 'modules/lessons/models/courses.set.php');

//--------------------------------------------------------------------------------------------------
//	administrative / devlopment action to rebuild the index.dat.php
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $kapenta->page->do403(); }

	echo $theme->expandBlocks("[[:theme::ifscrollheader:]]");

	echo $lessons_rebuild_index();

	echo $theme->expandBlocks("[[:theme::ifscrollfooter:]]");

?>
