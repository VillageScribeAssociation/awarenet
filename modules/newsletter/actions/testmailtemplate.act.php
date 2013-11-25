<?php

//--------------------------------------------------------------------------------------------------
//*	test / development action to show templated email
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }
	if ('' == $kapenta->request->ref) { $page->do404('please specify an edition'); }

	echo $theme->expandBlocks('[[:newsletter::mailtemplate::UID=' . $kapenta->request->ref . ':]]');

?>
