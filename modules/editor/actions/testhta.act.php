<?

//--------------------------------------------------------------------------------------------------
//*	development action to diagnose problems with HyperTextArea
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	if (true == array_key_exists('moose', $_POST)) {
		$session->msg('<b>Posted:</b><br/>' . $_POST['moose'], 'ok');
	}

	if ('yes' == $session->get('mobile')) {
		$kapenta->page->load('modules/editor/actions/testhta.page.php');
		$kapenta->page->render();
	} else {
		$kapenta->page->load('modules/editor/actions/testhta.m.page.php');
		$kapenta->page->render();
	}

?>
