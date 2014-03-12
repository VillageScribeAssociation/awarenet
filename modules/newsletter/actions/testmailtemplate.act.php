<?php

//--------------------------------------------------------------------------------------------------
//*	test / development action to show templated email
//--------------------------------------------------------------------------------------------------

	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }
	if ('' == $kapenta->request->ref) { $kapenta->page->do404('please specify an edition'); }

	echo $theme->expandBlocks('[[:newsletter::mailtemplate::UID=' . $kapenta->request->ref . ':]]');

?>
