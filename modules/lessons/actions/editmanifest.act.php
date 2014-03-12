<?php

//--------------------------------------------------------------------------------------------------
//*	test / development action for editing manifests
//--------------------------------------------------------------------------------------------------
//ref: UID of an installed lesson packages

	if ('admin'  != $kapenta->user->role) { $kapenta->page->do403(); }

	if (false == $kapenta->fs->exists('data/lessons/' . $kapenta->request->ref . '/manifest.xml')) { $kapenta->page->do404(); }

	$kapenta->page->load('modules/lessons/actions/editmanifest.page.php');
	$kapenta->page->blockArgs['UID'] = $kapenta->request->ref;
	$kapenta->page->blockArgs['manifestUID'] = $kapenta->request->ref;
	$kapenta->page->render();

?>
