<?php

	require_once($kapenta->installPath . 'modules/kjs/inc/viewport.class.php');

//--------------------------------------------------------------------------------------------------
//*	create a new Kapenta.JS module outline based on a kapenta module
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('action not given'); }
	if ('newModule' != $_POST['action']) { $kapenta->page->do404('action not recognized'); }

	if (false == array_key_exists('moduleName', $_POST)) { $kapenta->page->do404(''); }
	if (false == array_key_exists('baseName', $_POST)) { $kapenta->page->do404(''); }
	if (false == $kapenta->moduleExists($_POST['baseName'])) { $kapenta->page->do404('No such module.'); }

	$moduleName = trim($_POST['moduleName']);
	$baseName = trim($_POST['baseName']);

	echo $theme->expandBlocks("[[:theme::ifscrollheader:]]");

	//----------------------------------------------------------------------------------------------
	//	make basic directory structure
	//----------------------------------------------------------------------------------------------
	$kapenta->filemakeSubdirs('data/kjs/modules/' . $moduleName . '/null.txt');
	$kapenta->filemakeSubdirs('data/kjs/modules/' . $moduleName . '/models/null.txt');
	$kapenta->filemakeSubdirs('data/kjs/modules/' . $moduleName . '/actions/null.txt');
	$kapenta->filemakeSubdirs('data/kjs/modules/' . $moduleName . '/views/null.txt');
	$kapenta->filemakeSubdirs('data/kjs/modules/' . $moduleName . '/events/null.txt');
	$kapenta->filemakeSubdirs('data/kjs/modules/' . $moduleName . '/inc/null.txt');
	
	//----------------------------------------------------------------------------------------------
	//	copy across views
	//----------------------------------------------------------------------------------------------

	$viewport = new KJS_ViewPort($moduleName, $baseName);
	$viewport->copyBlocks();
	$viewport->copyViews();
	echo $viewport->report;

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------

	echo $theme->expandBlocks("[[:theme::ifscrollfooter:]]");

?>
