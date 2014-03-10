<?php

//--------------------------------------------------------------------------------------------------
//*	confirm receipt of a file, can be removed from local queue
//--------------------------------------------------------------------------------------------------

	if ('' == $kapenta->request->ref) { $kapenta->page->doXmlError('file not specified'); }

	$fileName = $kapenta->request->ref;
	$fileName = str_replace('..', '', $fileName);
	$fileName = str_replace('/', '', $fileName);
	$fileName = str_replace('\\', '', $fileName);

	$parts = explode('.', $fileName);
	
	$peerUID = $parts[2];

	$fileName = 'data/p2p/pending/' . $peerUID . '/' . $fileName;

	if (false == $kapenta->fs->exists($fileName)) { $kapenta->page->doXmlError('no such file'); }

	$check = $kapenta->fileDelete($fileName, true);
	if (true == $check) {
		echo "Deleted file: $fileName\n";
	} else {
		echo "Could not delete file: $fileName\n";
	}

	$lockFile = str_replace('.bz2', '.lock', $fileName);

	$check = $kapenta->fileDelete($lockFile, true);
	if (true == $check) {
		echo "Deleted lock file: $lockFile\n";
	} else {
		echo "Could not delete lock file: $lockFile\n";
	}

	echo "<ok/>";

?>
