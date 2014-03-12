<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	search for images which exceed images.maxsize and reduce to save disk space
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check user role and registry
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403('This is an administratice action.'); }
	if (false == $kapenta->registry->has('images.maxsize')) { $kapenta->registry->set('images.maxsize', '524288'); }

	$maxSize = $kapenta->registry->get('images.maxsize');

	//----------------------------------------------------------------------------------------------
	//	check all images
	//----------------------------------------------------------------------------------------------
	$sql = "select * from images_image";
	$result = $kapenta->db->query($sql);

	while ($row = $kapenta->db->fetchAssoc($result)) {
		$item = $kapenta->db->rmArray($row);
		if (true == $kapenta->fs->exists($item['fileName'])) {
			$fileSize = $kapenta->fs->size($item['fileName']);

			if ($fileSize >= $maxSize) {
				$model = new Images_Image($item['UID']);
				if ($model->loaded && $model->transforms->loaded) {
					$check = $model->transforms->reduce();
					if (false == $check) {
						$msg = ''
						 . "Could not rescale large image "
						 . "'" . $item['title'] . "' (" . $item['UID'] . ")";
						$kapenta->session->msg($msg, 'bad');
					}
				} else {
					$kapenta->session->msg('Model not loaded.');
				}
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to admin console
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('admin/');

?>
