<?php

//--------------------------------------------------------------------------------------------------
//*	action to delete database dumps from the export directory
//--------------------------------------------------------------------------------------------------
//ref: name of a file in /data/export/

	//----------------------------------------------------------------------------------------------
	//	check reference and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if ('' == trim($kapenta->request->ref)) { $page->do404('no filename given'); }

	$fileName = $kapenta->request->ref;
	$fileName = str_replace('/', '', $fileName);
	$fileName = str_replace('\\', '', $fileName);
	$fileName = str_replace('..', '', $fileName);

	$fileName = 'data/export/' . $fileName;

	if (false == $kapenta->fs->exists($fileName, true)) { $page->do404('File not found.'); }

	$check = $kapenta->fileDelete($fileName, true);

	if (true == $check) { $session->msgAdmin("Deleted: $fileName", 'ok'); }
	else { $session->msgAdmin("Coud not delete: $fileName", 'bad');	}

	$page->do302('admin/exportdb/');

?>
