<?php

	require_once($kapenta->installPath . 'modules/newsletter/models/notice.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a notice
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do404(); }

	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not given', true); }

	$model = new Newsletter_Notice($_POST['UID']);
	if (false == $model->loaded) { $page->do404('No such notice.', true); }

	$model->delete();

	//----------------------------------------------------------------------------------------------
	//	add javascript to close the window
	//----------------------------------------------------------------------------------------------

	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]');

	echo ''
	 . "<script>\n"
	 . "	var UID = window.name.replace('ifc', '');\n"
	 . "	if ((window.parent) && (window.parent.kwindowmanager)) {\n"
	 . "		var kwm = window.parent.kwindowmanager;\n"
	 . "		var hWnd = kwm.getIndex(UID);\n"
	 . "		window.parent.newsletter_removenotice('" . $model->UID . "');\n"
	 . "		window.parent.kwindowmanager.closeWindow(UID);\n"
	 . "	}\n"
	 . "</script>\n";

	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]');

?>
