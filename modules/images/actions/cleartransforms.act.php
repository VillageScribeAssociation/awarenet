<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete all transforms of all images
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }

	echo $theme->expandBlocks('[[:theme::ifscrollheader::title=Clearing image transforms:]]');

	//----------------------------------------------------------------------------------------------
	//	delete all transforms 
	//----------------------------------------------------------------------------------------------
	$result = $kapenta->db->query("select * from images_image");
	while($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);
		$model = new Images_Image($row['UID']);

		$msg = ''
		 . "<b>" . $model->title . "</b><br/>\n"
		 . "Clearing transforms for images_image::" . $model->UID . " (1)<br/>\n";

		$removed = '';

		foreach($model->transforms->presets as $label => $definition) {
			$fileName = str_replace('.jpg', '_' . $label . '.jpg', $model->fileName);
			if (true == $kapenta->fs->exists($fileName)) {
				$removed .= $label . ' --> ' . $fileName . "<br/>\n";
				$kapenta->fileDelete($fileName, true);
			}
		}

		if ('' !== $removed) {
			echo ''
			 . "<div class='chatmessagegreen'>\n"
			 . $msg
			 . $removed
			 . "</div>\n";
			flush();
		}

	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to admin console
	//----------------------------------------------------------------------------------------------
	echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]');

?>
