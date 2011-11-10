<?

//--------------------------------------------------------------------------------------------------
//*	development action to diagnose problems with HyperTextArea
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	if (true == array_key_exists('moose', $_POST)) {
		$session->msg('<b>Posted:</b><br/>' . $_POST['moose'], 'ok');
	}

	$page->load('modules/editor/actions/testhta.page.php');
	$page->render();

?>
