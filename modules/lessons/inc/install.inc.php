<?

	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');
	require_once($kapenta->installPath . 'modules/lessons/models/courses.set.php');
	require_once($kapenta->installPath . 'modules/lessons/models/stub.mod.php');
	require_once($kapenta->installPath . 'modules/lessons/models/collection.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install scripts for admin module
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//	install the admin module
//--------------------------------------------------------------------------------------------------
//returns: html report [string]

function lessons_install_module() {
	global $kapenta;
	global $kapenta;

	if ('admin' != $kapenta->user->role) { return false; }	// only admins can do this

	$report = "<h3>Installing Lessons Module</h3>\n";
	$dba = $kapenta->getDBAdminDriver();

	if (false == $kapenta->fs->exists('data/lessons/')) {
		$kapenta->fileMakeSubdirs('/data/lessons/x.txt');
	}

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Lessons_Stub table
	//----------------------------------------------------------------------------------------------
	$model = new Lessons_Stub();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade lessons_collection table
	//----------------------------------------------------------------------------------------------
	$model = new Lessons_collection();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create default registry values
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->registry->get('kalite.installation')) { 
		$kapenta->registry->set('kalite.installation', 'http://localhost:8008');
	}
	if ('' == $kapenta->registry->get('kalite.admin.user')) { 
		$kapenta->registry->set('kalite.admin.user', 'awarenet');
	}
	if ('' == $kapenta->registry->get('kalite.admin.pwd')) { 
		$kapenta->registry->set('kalite.admin.pwd', 'awarenet');
	}
	if ('' == $kapenta->registry->get('kalite.db.file')) { 
		$kapenta->registry->set('kalite.db.file', '/var/www/KALite/ka-lite/kalite/database/data.sqlite');
	}

	//------------------------------------------------------------------------------------------
	//	done
	//------------------------------------------------------------------------------------------
	return $report;
}

//--------------------------------------------------------------------------------------------------
//	discover if this module is installed
//--------------------------------------------------------------------------------------------------
//returns: HTML installation report [string]
// if installed correctly report will contain HTML comment <!-- installed correctly -->

function lessons_install_status_report() {
	global $kapenta;
	global $kapenta;

	if ('admin' != $kapenta->user->role) { return false; }	// only admins can do this
	$installed = true;
	$dba = $kapenta->getDBAdminDriver();
	$report = '';
	$installNotice = '<!-- table installed correctly -->';

	if (false == $kapenta->fs->exists('data/lessons/')) {
		$installed = $kapenta->fileMakeSubdirs('/data/lessons/x.txt');
	}

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores stub objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Lessons_Stub();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->getTableInstallStatus($dbSchema);
	if (false == strpos($report, $installNotice)) { $installed = false; }
	if (true == $installed) { $report .= "<!-- installed correctly -->"; }

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores collection objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Lessons_collection();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;


	return $report;
}

//--------------------------------------------------------------------------------------------------
//|	rebuild the index.dat.php file
//--------------------------------------------------------------------------------------------------
//returns: HTML installation report [string]
// if installed correctly report will contain HTML comment <!-- installed correctly -->

function lessons_rebuild_index() {
	//TODO: use a registry key for this

	$report = "";

	$groupset = new Lessons_Courses();
	$groups = $groupset->listGroups(); 

	foreach($groups as $group) {
		$set = new Lessons_Courses($group);
		$report .= "<h3>Rebuilding $group index</h3>" . $set->rebuild();
	}

	return $report;
}

//--------------------------------------------------------------------------------------------------
//|	create a database copy of each course 
//--------------------------------------------------------------------------------------------------

function lessons_import_all() {
	
}

?>
