<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	search for images which exceed images.maxsize and reduce to save disk space
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check user role and registry
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403('This is an administratice action.'); }
	if (false == $registry->has('images.maxsize')) { $registry->set('images.maxsize', '524288'); }

	$maxSize = $registry->get('images.maxsize');

	//----------------------------------------------------------------------------------------------
	//	check all images
	//----------------------------------------------------------------------------------------------
	$sql = "select * from images_image";
	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		$item = $db->rmArray($row);
		if (true == $kapenta->fileExists($item['fileName'])) {
			$fileSize = $kapenta->fileSize($item['fileName']);

			if ($fileSize >= $maxSize) {
				$model = new Images_Image($item['UID']);
				if ($model->loaded && $model->transforms->loaded) {
					$check = $model->transforms->reduce();
					if (false == $check) {
						$msg = ''
						 . "Could not rescale large image "
						 . "'" . $item['title'] . "' (" . $item['UID'] . ")";
						$session->msg($msg, 'bad');
					}
				} else {
					$session->msg('Model not loaded.');
				}
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to admin console
	//----------------------------------------------------------------------------------------------
	$page->do302('admin/');

?>
