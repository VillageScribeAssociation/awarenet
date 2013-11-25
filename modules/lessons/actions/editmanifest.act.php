<?php

//--------------------------------------------------------------------------------------------------
//*	test / development action for editing manifests
//--------------------------------------------------------------------------------------------------
//ref: UID of an installed lesson packages

	if ('admin'  != $user->role) { $page->do403(); }

	if (false == $kapenta->fs->exists('data/lessons/' . $req->ref . '/manifest.xml')) { $page->do404(); }

	$page->load('modules/lessons/actions/editmanifest.page.php');
	$kapenta->page->blockArgs['UID'] = $req->ref;
	$kapenta->page->blockArgs['manifestUID'] = $req->ref;
	$page->render();

?>
