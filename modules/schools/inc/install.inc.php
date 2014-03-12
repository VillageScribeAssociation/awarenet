<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for Schools module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Schools module
//--------------------------------------------------------------------------------------------------
//returns: html report or false if not authorized [string][bool]

function schools_install_module() {
	global $kapenta;
	global $kapenta;
	global $kapenta;
	global $kapenta;

	if ('admin' != $kapenta->user->role) { return false; }

	$report = '';

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Schools_School table
	//----------------------------------------------------------------------------------------------
	$model = new Schools_School();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	import any records from previous schools table
	//----------------------------------------------------------------------------------------------
	$rename = array('recordAlias' => 'alias');
	$count = $dba->copyAll('schools', $dbSchema, $rename); 
	$report .= "<b>moved $count records from 'schools' table.</b><br/>";

	//----------------------------------------------------------------------------------------------
	//	install a default school if none present
	//----------------------------------------------------------------------------------------------

	$conditions = array("UID <> ''");
	$totalSchools = $kapenta->db->countRange('schools_school', $conditions);
	
	if (0 == $totalSchools) {
		//------------------------------------------------------------------------------------------
		//	create a school
		//------------------------------------------------------------------------------------------
		$model = new Schools_school();
		$model->name = "First School";
		$model->description = "Descriibe your school here.";
		$model->region = "Eastern Cape";
		$model->region = "South Africa";
		$model->type = "High School";
		$check = $model->save();
		
		if ('' == $check) {
			$report .= "Created default school with UID " . $model->UID . ".<br/>";
			$kapenta->registry->set('firstrun.firstschool', $model->UID);
		} else {
			$report .= "Could not create default school record:<br/>\n$check<br/>\n";
		}
	} else {
		//------------------------------------------------------------------------------------------
		//	get oldest school record
		//------------------------------------------------------------------------------------------
		$range = $kapenta->db->loadRange('schools_school', '*', '', 'createdOn', '1');
		foreach($range as $item) {
			$kapenta->registry->set('firstrun.firstschool', $item['UID']);
			$report .= "Set default school to oldest record: " . $item['name'] . "<br/>\n";
		}
	}
	
	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;
}

//--------------------------------------------------------------------------------------------------
//|	discover if this module is installed
//--------------------------------------------------------------------------------------------------
//:	if installed correctly report will contain HTML comment <!-- installed correctly -->
//returns: HTML installation status report [string]

function schools_install_status_report() {
	global $kapenta;
	global $kapenta;

	if ('admin' != $kapenta->user->role) { return false; }

	$report = '';
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores School objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Schools_School();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	if (true == $installed) { $report .= '<!-- module installed correctly -->'; }
	return $report;
}

?>
