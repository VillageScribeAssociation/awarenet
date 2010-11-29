<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//-------------------------------------------------------------------------------------------------
//	maintain the gallery table
//-------------------------------------------------------------------------------------------------

function gallery_maintenance() {
	global $db, $theme;

	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;

	$report = "<h2>Checking gallery table...</h2>";

	//---------------------------------------------------------------------------------------------
	//	check image count of all gallery tables
	//---------------------------------------------------------------------------------------------

	$errors = array();
	$errors[] = array('UID', 'Title', 'Image Count', 'error');

	$sql = "select * from Gallery_Gallery";
	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$recorded = $row['imagecount'];

		//-----------------------------------------------------------------------------------------
		//	count images belonging to this gallery
		//-----------------------------------------------------------------------------------------
		$countBlock = " [[:images::count::refModule=gallery::refUID=" . $row['UID'] . ":]] ";
		$measured = trim($theme->expandBlocks($countBlock, ''));

		//-----------------------------------------------------------------------------------------
		//	change recorded value if wrong
		//-----------------------------------------------------------------------------------------
		if ($measured != $recorded) {
			$model = new Gallery_Gallery();
			$model->loadArray($row);
			$model->imagecount = $measured;
			$model->save();
			$errorCount++;
			$fixCount++;
		}

		//-----------------------------------------------------------------------------------------
		//	make sure this gallery has an owner name
		//-----------------------------------------------------------------------------------------
		if ('' == $row['ownerName']) {
			$ownerNameBlock = '[[:users::name::userUID=' . $row['createdBy'] . ':]]';
			$ownerName = $theme->expandBlocks($ownerNameBlock, '');

			$model = new Gallery_Gallery();
			$model->loadArray($row);
			$model->ownerName = $ownerName;
			$model->save();
			$errorCount++;
			$fixCount++;			
		}


		//-----------------------------------------------------------------------------------------
		//	make sure this gallery has a school name
		//-----------------------------------------------------------------------------------------
		if ('' == $row['schoolName']) {
			$schoolNameBlock = '[[:users::schoolname::link=no::userUID=' . $row['createdBy'] . ':]]';
			$schoolName = $theme->expandBlocks($schoolNameBlock, '');
			$model = new Gallery_Gallery();
			$model->loadArray($row);
			$model->schoolName = $schoolName;
			$model->save();
			$errorCount++;
			$fixCount++;			
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
