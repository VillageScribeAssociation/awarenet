<?php

//--------------------------------------------------------------------------------------------------
//*	temporary administrative script to create a windows batch file to schedule worker threads
//--------------------------------------------------------------------------------------------------

	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	echo "<pre>\n";

	for ($hour = 0; $hour < 24; $hour++) {
		for ($minute = 0; $minute < 6; $minute++) {

			$line = ''
			 . "AT"
			 . " " . substr('0' . $hour, -2) . ":" . $minute . "0:00 /every:montag,dienstag,mittwoch,donnerstag,freitag,samstag,sonntag"
			 . " \"cmd /c c:\\awareNet\\cronsingle.bat\"\n";

			echo $line;

		}
	}

	echo "</pre>";

?>
