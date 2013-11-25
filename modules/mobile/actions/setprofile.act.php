<?php

//--------------------------------------------------------------------------------------------------
//*	Set user agent mobile profile
//--------------------------------------------------------------------------------------------------

	$profiles = array('desktop', 'mobile', 'tablet');

	if (true == in_array($kapenta->request->ref, $profiles)) {

		$session->set('deviceprofile', $kapenta->request->ref);
		$page->do302('/');

	} else {
		$page->do404('Unknown device profile.');
	}

?>
