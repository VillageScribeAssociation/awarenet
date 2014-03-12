<?

//--------------------------------------------------------------------------------------------------
//*	log the user out and redirect to the homepage
//--------------------------------------------------------------------------------------------------

	$kapenta->session->set('recover', 'no');

	if ('public' == $kapenta->session->user) {
		//------------------------------------------------------------------------------------------
		//	user was not logged in
		//------------------------------------------------------------------------------------------
		$kapenta->session->msg("You are already logged out.<br/>\n");
		$kapenta->page->do302(''); // homepage		

	} else {
		//------------------------------------------------------------------------------------------
		//	log them out
		//------------------------------------------------------------------------------------------
		$args = array('userUID' => $kapenta->session->user);

		if ('yes' == $kapenta->session->get('recover')) { $kapenta->session->set('recover', 'no'); }
		$check = $kapenta->session->logout();

		if (true == $check) { $kapenta->session->msg("You are now logged out.<br/>\n", 'ok'); }
		else { $kapenta->session->msg('Logout incomplete.', 'bad'); }

		$kapenta->raiseEvent('*', 'users_logout', $args);
		$kapenta->page->do302('');

	}

?>
