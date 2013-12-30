<?

	if ('' == $kapenta->request->ref) {
		include $kapenta->installPath . 'modules/tutorials/actions/disassemble.act.php';
	} else {
		include $kapenta->installPath . 'modules/tutorials/actions/assemble.act.php';
	}

?>
