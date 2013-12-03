<?php

//--------------------------------------------------------------------------------------------------
//*	action to earch for and replace binary unsafe string functions
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	$mbfns = array(
		'strpos' => 'mb_strpos',
		'strlen' => 'mb_strlen',
		'strtolower' => 'mb_strtolower',
		'strtoupper' => 'mb_strtoupper',
		'substr' => 'mb_substr',
	);

	$files = array(
		'core/kutils.class.php',
		'core/khtml.class.php',
		'core/ktheme.class.php'
	);

	foreach($files as $file) {

		$raw = $kapenta->fs->get($file);

		foreach($mbfns as $from => $to) {
			$raw = str_replace($from, $to, $raw);
			$raw = str_replace('mb_' . $to, $to, $raw);
		}

		$kapenta->fs->put($file, $raw);

		header("Content-type: text/plain");
		echo $file . "\n";
	}

?>
