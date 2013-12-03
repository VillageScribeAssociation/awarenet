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
			$testFile = 'data/images/unavailable/unavailable_' . $kapenta->request->args['s'] . '.jpg';
			if (true == $kapenta->fs->exists($testFile)) { $size = $kapenta->request->args['s']; }
	}

	$page->do302('data/images/unavailable/unavailable_' . $size . '.jpg');

?>
