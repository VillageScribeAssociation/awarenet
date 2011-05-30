<?

//--------------------------------------------------------------------------------------------------
//*	test tablele
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	$peerUID = '128205975917440492';	// mothsorchid
	$table = 'moblog_post';

	$change = $sync->getTableLe($peerUID, $table);

	echo "Total Objects: " . $change['total'] . "<br/>\n";
	echo "Dirty Objects: " . $change['dirty'] . "<br/>\n";
	echo "URL: " . $change['url'] . "<br/>\n";

	foreach($change['update'] as $UID) {
		echo "update object: $table $UID <br/>\n";
	}

?>
