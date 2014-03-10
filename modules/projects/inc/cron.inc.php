<?

	require_once($kapenta->installPath . 'modules/projects/models/section.mod.php');

//--------------------------------------------------------------------------------------------------
//*	processes run regularly to keep things tidy
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	projects ten minute cron
//--------------------------------------------------------------------------------------------------
//returns: HTML report of any actions taken [string]

function projects_cron_tenmins() {
	global $kapenta;
	global $kapenta;

	$report = "<h2>projects_cron_tenmins</h2>\n";		//%	return value [string]
	$lockTimeout = 600;									//%	ten minutes, TODO: registry [int]

	//----------------------------------------------------------------------------------------------
	//	load all locked project sections and remove any expired locks
	//----------------------------------------------------------------------------------------------
	$conditions = array("lockedBy != ''");
	$range = $kapenta->db->loadRange('projects_section', '*', $conditions);
	
	foreach($range as $item) {
		$expires = $kapenta->strtotime($item['lockedOn']) + $lockTimeout;
		$currTime = $kapenta->time();
		if ($currTime > $expires) {
			// note: loading Projects_Section object with check and fix its lock
			$model = new Projects_Section($item['UID']);
			//$check = $model->save();
			/*
			if ('' == $check) {
				$report .= ''
				 . "[i] Cleared expired lock on section " . $model->title
				 . " (UID: " . $model->UID . ") (projectUID: " . $model->projectUID . ")<br/>\n";
			} else {
				$report .= ''
				 . "[!] Could not clear expired lock on section " . $model->title
				 . " (UID: " . $model->UID . ") (projectUID: " . $model->projectUID . ")<br/>\n"
				 . "<br/>\n";
			}
			*/
		}
	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;
}

?>
