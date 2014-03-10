<?

/*
	require_once($kapenta->installPath . 'modules/live/inc/upload.class.php');

//--------------------------------------------------------------------------------------------------
//*	TEST development action - stitch all parts together
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $kapenta->page->do403(); }

	$all = '';
	$upload = new Live_Upload('9956abbde74a56f2bf6503a71f6ade2935254390');
	$upload->stitchTogether();	

	/*
	foreach($upload->parts as $part) {
		echo $part['fileName'] . "<br/>\n";

		$raw = $kapenta->fs->get($part['fileName'], true, true);
		echo "<textarea rows='10' style='width: 100%'>$raw</textarea><br/>\n";

		$data = base64_decode($raw);

		echo "<textarea rows='10' style='width: 100%'>$data</textarea><br/>\n";

		$all .= $data; 

	}
	
	echo "<h2>file</h2>\n";
	echo "<textarea rows='10' style='width: 100%'>$all</textarea><br/>\n";
	*/

	for ($i = 0; $i < 20; $i++) {
		echo substr($kapenta->createUID(), 0, 5) . "<br/>\n";
	}

?>
