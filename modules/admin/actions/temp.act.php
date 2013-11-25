<?

	require_once($kapenta->installPath . 'modules/comments/events/object_updated.on.php');

//-------------------------------------------------------------------------------------------------
//	look for spurious notifications
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	admins only
	//---------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	$tempUID = '';

	for ($i = 0; $i < 16; $i++) {

		list($usec, $sec) = explode(' ', microtime());				//	make a seed for rand() ...
		$seed = strrev(($sec . (string)($usec * 1000000)));			//	is only needed for older PHP

		if (true == function_exists('time_nanosleep')) {
			$nano_interval = (int)strrev($sec) % 100000;
			echo "Nanosleep: " . $nano_interval . "<br/>";
			time_nanosleep(0, $nano_interval);
		}

		echo "Microseconds: " . (string)($usec * 1000000) . "<br/>";
		echo "Seed: " . $seed . "<br/>";

		echo $sec . "<br/>\n";

		srand($seed);

		$digit = (int)rand(0, 35);

		echo "Digit: $digit<br/>\n";

		if ($digit < 10) {
			$tempUID .= $digit;
		} else {
			$tempUID .= chr(87 + $digit);
		}

	}

	echo "tempUID: $tempUID<br/>\n";

?>
