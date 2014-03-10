<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//-------------------------------------------------------------------------------------------------
//*	maintain the images module
//-------------------------------------------------------------------------------------------------

function images_maintenance() {
		global $kapenta;
		global $kapenta;
		global $theme;
		global $aliases;

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
	$result = $kapenta->db->query($sql);

	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);
		$model = new Images_Image($row['UID']);

		//-----------------------------------------------------------------------------------------
		//	check that this object has a reference
		//-----------------------------------------------------------------------------------------
		if ('' == $model->refUID) {
			$kapenta->db->delete($model->UID, $model->dbSchema);
			$errorCount++;
			$errors[] = array($row['UID'], $row['title'], 'No reference.');
		}

		//-----------------------------------------------------------------------------------------
		//	clean up legacy undelete scheme
		//-----------------------------------------------------------------------------------------

		/*
		if ($model->refUID != str_replace('del-', '', $model->refUID)) {
			$model->save();
			$errorCount++;
			$errors[] = array($row['UID'], $row['title'], 'legacy undelete issue');
		}

		if ($model->refModule != str_replace('del-', '', $model->refModule)) {
			$model->save();
			$errorCount++;
			$errors[] = array($row['UID'], $row['title'], 'legacy undelete issue');
		}

		if (('videos_video' == $model->refModel) && ('videos' != $model->refModule)) {
			$model->refModule = 'videos';
			$model->save();
			$errorCount++;
			$errors[] = array($row['UID'], $row['title'], 'legacy undelete issue');
		}
		*/

		//-----------------------------------------------------------------------------------------
		//	check that this object has an alias
		//-----------------------------------------------------------------------------------------
		if ('' == trim($model->alias)) {
			echo "no alias recorded on object, creating.<br/>\n";
			$model->save();
			$errorCount++;
			$model->load($row['UID']);
			if ('' == trim($model->alias)) {
				// not fixed
				$errors[] = array($row['UID'], $row['title'], '*could not add alias');
			} else {
				// fixed
				$errors[] = array($row['UID'], $row['title'], '*added alias');
				$fixCount++;
			}
		}

		$range = $aliases->getAll('images', 'images_image', $model->UID);
		if (0 == count($range)) {
			echo "Image does not own any objects in aliases table, creating.<br/>\n";
			$report = $model->save();			//	should create and save a new default alias
			if ('' == $report) {			
				$errorCount++;
				if ('' == trim($model->alias)) {
					$errors[] = array($row['UID'], $row['title'], 'could not add alias');
				} else {
					$errors[] = array($row['UID'], $row['title'], 'added alias');
					$fixCount++;
				}
			} else {
				echo $report . "<br/>\n";
			}
		}

		//-------------------------------------------------------------------------------------
		//	add hash if not already done and file is available
		//-------------------------------------------------------------------------------------
		if (('' == $model->hash) && (true == $kapenta->fs->exists($model->fileName))) {
			$model->save();			// hash is set by $model->vertify()
			$errors[] = array($row['UID'], $row['title'], 'added hash');
			$errorCount++;
			$fixCount++;
		}

		//-------------------------------------------------------------------------------------
		//	make sure image file is in /data/images/ (security)
		//-------------------------------------------------------------------------------------
		if ('data/images/' == substr($model->fileName, 0, 12)) {
			//--------------------------------------------------------------------------------------
			//	check that image file is valid
			//--------------------------------------------------------------------------------------
			/*
			if (true == file_exists($kapenta->installPath . $model->fileName)) {
				$loaded = $model->loadImage();
				if (false == $loaded) {
					unlink($kapenta->installPath . $model->fileName);
					$errors[] = array($row['UID'], $row['title'], 'removed broken image file');
					$errorCount++;
					$fixCount++;
				}
			}
			*/

		} else {
			//TODO:
			echo "TODO: remove this record (security)<br/>";
			//$model->fileName = '/data/images/error';
			//$model->save();
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
