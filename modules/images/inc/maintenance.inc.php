<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//-------------------------------------------------------------------------------------------------
//	maintain the images table
//-------------------------------------------------------------------------------------------------

function images_maintenance() {
	global $db, $kapenta, $theme;
	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;

	$report = "<h2>Checking images table...</h2>";

	//---------------------------------------------------------------------------------------------
	//	check image data
	//---------------------------------------------------------------------------------------------

	$errors = array();
	$errors[] = array('UID', 'Title', 'error');

	$sql = "select * from images_image";
	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$model = new Images_Image();
		$model->loadArray($row);

		//-----------------------------------------------------------------------------------------
		//	check that this record has a recordAlias
		//-----------------------------------------------------------------------------------------
		if ('' == trim($row['alias'])) {
			$model->save();
			$errorCount++;
			$model->load($row['UID']);
			if ('' == trim($model->alias)) {
				// not fixed
				$errors[] = array($row['UID'], $row['title'], 'added alias');
			} else {
				// fixed
				$errors[] = array($row['UID'], $row['title'], 'added alias');
				$fixCount++;
			}
		}


		//-------------------------------------------------------------------------------------
		//	make sure image file is in /data/images/ (security)
		//-------------------------------------------------------------------------------------
		if ('data/images/' == substr($model->fileName, 0, 12)) {
			//--------------------------------------------------------------------------------------
			//	check that image file is valid
			//--------------------------------------------------------------------------------------
			if (true == file_exists($kapenta->installPath . $model->fileName)) {
				$loaded = $model->loadImage();
				if (false == $loaded) {
					unlink($kapenta->installPath . $model->fileName);
					$errors[] = array($row['UID'], $row['title'], 'removed broken image file');
					$errorCount++;
					$fixCount++;
				}
			}

		} else {
			//TODO:
			echo "TODO: remove this record (security)<br/>";
			//$model->fileName = '/data/images/error';
			//$model->save();
		}

		//-----------------------------------------------------------------------------------------
		//	check transforms
		//-----------------------------------------------------------------------------------------
		foreach($model->transforms as $transName => $transFile) {
			//-------------------------------------------------------------------------------------
			//	make sure it's in /data/images
			//-------------------------------------------------------------------------------------
			if ('data/images/' == substr($transFile, 0, 12)) {
				if (true == file_exists($kapenta->installPath . $transFile)) {		// file exists
					if (false != strpos($transFile, '.jpg')) {				// has extension .jpg
						$img = @imagecreatefromjpeg($kapenta->installPath . $transFile);
						if (false == $img) {								// is not valid image
							unlink($kapenta->installPath . $transFile);		// delete it
							$label = 'removed broken transform (' . $transName . ')';
							$errors[] = array($row['UID'], $row['title'], $label);
							$errorCount++;
							$fixCount++;						
						}
					}
				}

			} else {
				//TODO:
				echo "NOT in /data/images/ TODO: remove this transform";
			}

		}		

		$recordCount++;
	}

	//---------------------------------------------------------------------------------------------
	//	compile report
	//---------------------------------------------------------------------------------------------

	if (count($errors) > 1) { $report .= $theme->arrayToHtmlTable($errors, true, true); }

	$report .= "<b>Records Checked:</b> $recordCount<br/>\n";
	$report .= "<b>Errors Found:</b> $errorCount<br/>\n";
	if ($errorCount > 0) {
		$report .= "<b>Errors Fixed:</b> $fixCount<br/>\n";
	}

	return $report;
}

?>
