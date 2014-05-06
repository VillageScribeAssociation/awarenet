<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	processes run regularly to keep things tidy
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	image module daily cron
//--------------------------------------------------------------------------------------------------
//returns: HTML report of any actions taken [string]

function images_cron_daily() {
	global $kapenta;

	$report = "<h2>images_cron_daily</h2>\n";	//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	add hashes to any images which are missing them
	//----------------------------------------------------------------------------------------------

	$sql = "select * from images_image where hash=''";

	$result = $kapenta->db->query($sql);
	while ($row = $kapenta->db->fetchAssoc($result)) {
		$item = $kapenta->db->rmArray($row);
		if (('' != $item['fileName']) && (true == $kapenta->fs->exists($item['fileName']))) {

			$model = new Images_Image($item['UID']);

			$model->hash = $kapenta->fileSha1($item['fileName']);

			$report .= ''
			 . "Adding hash to image: " . $item['UID'] . " (" . $item['fileName'] . ")<br/>\n"
			 . "fileSha1: " . $model->hash . "<br/>\n";

			$check = $model->save();
			if ('' == $check) { $report .= "...done<br/>\n"; }
			else { $report .= "Could not save image: $check<br/>\n"; }

		}
	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;
}

?>
