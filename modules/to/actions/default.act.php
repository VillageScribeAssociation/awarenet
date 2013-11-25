<?php

	require_once($kapenta->installPath . 'modules/pages/models/page.mod.php');

//-------------------------------------------------------------------------------------------------
//*	default action is to rediredct
//-------------------------------------------------------------------------------------------------

	if ('' == $kapenta->request->ref) { $page->do404(); }

	$model = new Pages_Page($kapenta->request->ref);

	if (false == $model->loaded) { $page->do404(); }

	//---------------------------------------------------------------------------------------------
	//	log this for analytic purposes
	//---------------------------------------------------------------------------------------------

	$log = ''
	 . "<refer>"
	 . "<datetime>" . $kapenta->datetime() . "</datetime>"
	 . "<ip>" . $_SERVER['REMOTE_ADDR'] . "</ip>"
	 . "<ip>" . $_SERVER['REMOTE_HOST'] . "</ip>"
	 . "</refer>";
	$fileName = 'data/out/' . $model->UID . '/' . date("Y-m-d.xml");
	$kapenta->fs->put($fileName, $log, true, false, 'a+');

	//---------------------------------------------------------------------------------------------
	//	do the redirect
	//---------------------------------------------------------------------------------------------

	//header("HTTP/1.1 301 Moved Permanently");
	//header("Location: " . $model->url);

?>
