<?

//--------------------------------------------------------------------------------------------------
//*	temporary action for testing javascript callback
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	if ('admin' != $user->role) { $page->do403(); }

	echo "POST VARS<br/>\n";
	foreach($_POST as $key => $value) {
		echo "$key := $value <br/>\n";
	}

?>
