<?php

	require_once($kapenta->installPath . 'modules/newsletter/models/subscription.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a subscription
//--------------------------------------------------------------------------------------------------
//postarg: UID - UID of a Newsletter_Subscription object to delete [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------

	if ('admin' != $kapenta->user->role) { $kapenta->page->do403("Not authorized", true); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('UID not given', true); }

	$model = new Newsletter_Subscription($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Subscription not found', true); }

	//----------------------------------------------------------------------------------------------
	//	delete it and close the window
	//----------------------------------------------------------------------------------------------
	
	$model->delete();

	echo ''
	 . "<script>\n"
	 . "	var UID = window.name.replace('ifc', '');\n"
	 . "	if ((window.parent) && (window.parent.kwindowmanager)) {\n"
	 . "		var kwm = window.parent.kwindowmanager;\n"
	 . "		var hWnd = kwm.getIndex(UID);\n"
	 . "		window.parent.newsletter_reloadsubscriptions();\n"
	 . "		window.parent.kwindowmanager.closeWindow(UID);\n"
	 . "	}\n"
	 . "</script>\n";

?>
