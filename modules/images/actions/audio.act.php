<?

	require_once($kapenta->installPath . 'modules/images/models/transforms.set.php');

//--------------------------------------------------------------------------------------------------
//*	renders the 'not found' image at the specified size
//--------------------------------------------------------------------------------------------------
//reqopt: s - size label (eg, full, slide, width500) [string]

	//----------------------------------------------------------------------------------------------
	//	show the image
	//----------------------------------------------------------------------------------------------
	$size = 'width300';

	$set = new Images_Transforms();

	if (
		(true == array_key_exists('s', $kapenta->request->args)) &&
		(true == array_key_exists($kapenta->request->args['s'], $set->presets))
	) {
			$testFile = 'modules/videos/assets/audio-icon_' . $kapenta->request->args['s'] . '.png';
			if (true == $kapenta->fs->exists($testFile)) { $size = $kapenta->request->args['s']; }
	}

	$page->do302('modules/videos/assets/audio-icon_' . $size . '.png');

?>
