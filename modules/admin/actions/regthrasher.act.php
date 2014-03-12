<?php

//--------------------------------------------------------------------------------------------------
//*	test/development action to thrash the registry looking for problems
//--------------------------------------------------------------------------------------------------

	//if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	$keys = explode(' ', "Btrfs is under heavy development but every effort is being made to keep the filesystem stable and fast because of the speed of development, you should run the latest kernel you can (either the latest release kernel from kernel.org or the latest -rc kernel. Please email the Btrfs mailing list if you have any problems or questions while using Btrfs");

	$num = count($keys) - 1;

	$startTime = 0.00 + time() + microtime();

	for ($i = 0; $i < 10000; $i++) {
		$key = 'test.' . strtolower($keys[rand(0, $num)]);
		//echo 'key: ' . $key . "<br/>\n";

		if (2 == rand(0, 3)) { $kapenta->registry->set($key, rand(0, 1000)); }
		else { $kapenta->registry->get($key); }
	}

	$kapenta->registry->set('test.canary', 'alive');

	for ($i = 0; $i < 10000; $i++) {
		$key = 'test.' . strtolower($keys[rand(0, $num)]);
		//echo 'key: ' . $key . "<br/>\n";

		if (2 == rand(0, 3)) { $kapenta->registry->set($key, rand(0, 1000)); }
		else { $kapenta->registry->get($key); }
	}

	$endTime = 0.00 + time() + microtime();

	if ('alive' != $kapenta->registry->get('test.canary')) { echo "<b>canary died.</b><br/>\n"; }
	else { echo "<b>canary survived</b><br/>\n"; }

	echo "<small>20000 tests in " . ($endTime - $startTime) . " microseconds.</small><br/>";

?>
