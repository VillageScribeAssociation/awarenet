<?php
	
	require_once($kapenta->installPath . 'modules/images/models/images.set.php');
	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display the default image associated with something
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $kapenta->request->args)) { $kapenta->page->do404('refModule not given'); }
	if (false == array_key_exists('refModel', $kapenta->request->args)) { $kapenta->page->do404('refModel not given'); }
	if (false == array_key_exists('refUID', $kapenta->request->args)) { $kapenta->page->do404('refUID not given'); }

	$refModule = $kapenta->request->args['refModule'];
	$refModel = $kapenta->request->args['refModel'];
	$refUID = $kapenta->request->args['refUID'];
	$size = 'full';

	if (false == $kapenta->moduleExists($refModule)) { $kapenta->page->do404('Unknown module'); }
	if (false == $kapenta->db->objectExists($refModel, $refUID)) { $kapenta->page->do404('Unknown owner'); }

	//TODO: permissions check here

	if (true == array_key_exists('s', $kapenta->request->args)) { $req_args['size'] = $kapenta->request->args['s']; }
	if (true == array_key_exists('size', $kapenta->request->args)) { $size = $kapenta->request->args['size']; }

	//----------------------------------------------------------------------------------------------
	//	load set of images related to this object
	//----------------------------------------------------------------------------------------------
	$set = new Images_Images($refModule, $refModel, $refUID);
	
	if (0 == count($set->members)) { 
		$file = new Videos_Video($refUID);
		if (false !== strpos($file->fileName, 'mp3')) {
			$kapenta->page->do302('images/audio/s_' . $size . '/');
		} else {
			$kapenta->page->do302('images/unavailable/s_' . $size . '/'); 
		}
	}	
	$defaultAry = $set->members[0];

	$kapenta->page->do302('images/s_' . $size . '/' . $defaultAry['alias']);

?>
