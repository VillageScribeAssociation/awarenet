<?php

	if ('admin' !== $kapenta->user->role) { $kapenta->page->do403(); }
	echo shell_exec('gpg --list-keys');

?>
