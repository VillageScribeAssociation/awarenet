<?

	require_once($kapenta->installPath . 'modules/revisions/models/deleted.mod.php');

//--------------------------------------------------------------------------------------------------
//*	processes run regularly to keep things tidy
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	daily cron
//--------------------------------------------------------------------------------------------------
//returns: HTML report of any actions taken [string]

function revisions_cron_daily() {
	global $kapenta;
	global $db;

	$report = "<h2>revisions_cron_daily</h2>\n";						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	ensure undeleted items are correctly undeleted
	//----------------------------------------------------------------------------------------------

	$report .= "<h2>Checking undelete</h2>";

	$sql = "SELECT * FROM revisions_deleted WHERE status='" . $db->addMarkup('restore') ."'";
	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		$item = $db->rmArray($row);
		if (false == $db->obejctExists($item['refModel'], $item['refUID'])) {

			$model = new Revisions_Deleted($item['UID']);
			if (true == $model->loaded) {
				$check = $model->restore();
				if (true == $check) { 
					$report .= "Undeleted ". $item['refModel'] ."::". $item['refUID'] ."<br/>\n";
				} else {
					$report .= "Error: ". $item['refModel'] ."::". $item['refUID'] ."<br/>\n";
				}
			} else {
				$report .= "Could not load revisions_deleted::" . $item['UID'] . "<br/>\n";
			}

		}
	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;
}

?>
