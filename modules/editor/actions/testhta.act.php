<?

//--------------------------------------------------------------------------------------------------
//*	development action to diagnose problems with HyperTextArea
//--------------------------------------------------------------------------------------------------

	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	if (true == array_key_exists('moose', $_POST)) {
		$kapenta->session->msg('<b>Posted:</b><br/>' . $_POST['moose'], 'ok');
	}

	if ('yes' == $kapenta->session->get('mobile')) {
		$kapenta->page->load('modules/editor/actions/testhta.page.php');
		$kapenta->page->render();
	} else {
		$kapenta->page->load('modules/editor/actions/testhta.m.page.php');
		$kapenta->page->render();
	}

?>
