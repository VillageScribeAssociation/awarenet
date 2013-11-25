<?php

//--------------------------------------------------------------------------------------------------
//*	test / development action which simply displays the current user agent
//--------------------------------------------------------------------------------------------------

	if (
		(true == isset($_SERVER)) &&
		(true == array_key_exists('HTTP_USER_AGENT', $_SERVER))
	) {
		echo "<b>HTTP_USER_AGENT:</b> " . $_SERVER['HTTP_USER_AGENT'] . "<br/>\n";
		echo "<b>Device profile:</b> " . $kapenta->request->guessDeviceProfile() . " <br/>";
		$kapenta->fs->put('data/temp/lastdevice.txt', $_SERVER['HTTP_USER_AGENT'], 'w+');
	} else {
		echo "Unknown";
	}

?>
