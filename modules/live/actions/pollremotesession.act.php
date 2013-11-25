<?php

//--------------------------------------------------------------------------------------------------
//*	check result of poll session
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->doXmlError('Admins only'); }
	if ('' == $kapenta->request->ref) { $page->doXmlError('Remote shell session not specified.'); }

	$sUID = $kapenta->request->ref;
	$fileName = 'data/live/remoteshell/' . $sUID . '.txt';

	if (false == $kapenta->fs->exists($fileName)) {
		echo ". ";
		die();
	} 
	
	$raw = $kapenta->fs->get($fileName);
	$kapenta->fileDelete($fileName, true);
	echo "<br/>\n" . $raw;

?>
