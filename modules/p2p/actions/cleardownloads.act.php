<?php

//-------------------------------------------------------------------------------------------------
//*	action to clear current download queue (delete all file parts and manifests)
//-------------------------------------------------------------------------------------------------
//:	mostly used in deveopment / testing

	if ('admin' != $user->role) { $kapenta->page->do403(); }

	//---------------------------------------------------------------------------------------------
	//	delete manifests
	//---------------------------------------------------------------------------------------------
	$files = $kapenta->fs->listDir('data/p2p/transfer/meta/');
	foreach($files as $metaFile) {
		$kapenta->fileDelete($metaFile);
	}

	//---------------------------------------------------------------------------------------------
	//	delete file parts
	//---------------------------------------------------------------------------------------------

	$files = $kapenta->fs->listDir('data/p2p/transfer/parts/');
	foreach($files as $partFile) {
		$kapenta->fileDelete($partFile);
	}

	$session->msg('Cleared download queue.', 'ok');
	$kapenta->page->do302('p2p/peers/');

?>
