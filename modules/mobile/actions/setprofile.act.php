<?php

//--------------------------------------------------------------------------------------------------
//*	Set user agent mobile profile
//--------------------------------------------------------------------------------------------------

	$profiles = array('desktop', 'mobile', 'tablet');

	if (true == in_array($kapenta->request->ref, $profiles)) {

		$kapenta->session->set('deviceprofile', $kapenta->request->ref);
		$kapenta->page->do302('/');

	} else {
		$kapenta->page->do404('Unknown device profile.');
	}

?>
