<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');
	require_once($kapenta->installPath . 'modules/files/models/folder.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for Files module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Files module
//--------------------------------------------------------------------------------------------------
//returns: html report or false if not authorized [string][bool]

function files_install_module() {
	global $db;
	global $user;
	global $kapenta;
	global $kapenta;

	if ('admin' != $user->role) { return false; }
	$report = '';

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Files_File table
	//----------------------------------------------------------------------------------------------
	$model = new Files_File();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	copy all records from previous table
	//----------------------------------------------------------------------------------------------
	$rename = array('recordAlias' => 'alias');
	$count = $dba->copyAll('files', $dbSchema, $rename); 
	$report .= "<b>moved $count records from 'files' table.</b><br/>";

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Files_Folder table
	//----------------------------------------------------------------------------------------------
	$model = new Files_Folder();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	copy all records from previous table
	//----------------------------------------------------------------------------------------------
	$rename = array('recordAlias' => 'alias');
	$count = $dba->copyAll('folders', $dbSchema, $rename); 
	$report .= "<b>moved $count records from 'folders' table.</b><br/>";

	//----------------------------------------------------------------------------------------------
	//	create file associations
	//----------------------------------------------------------------------------------------------

	$assoc = array(			//	Allowed file types:
		'doc',				//	Microsoft Word Documents
		'odt',				//	OpenOffice Documents
		'pdf',				//	Adobe Portable Document Format
		'ppt',				//	Microsoft Powerpoint Slide Decks
		'tar',				//	Tarballs
		'tar.gz',			//	Gzipped Tarballs
		'txt',				//	Plain Text Documents
		'zip'				//	ZIP archives
	);

	foreach($assoc as $ext) {
		if ('files' != $kapenta->registry->get('live.file.' . $ext)) {
			$kapenta->registry->set('live.file.' . $ext, 'files');
			$report .= "<b>Added file association:</b> $ext<br/>";
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

function files_install_status_report() {
	global $user;
	global $kapenta;

	if ('admin' != $user->role) { return false; }

	$report = '';
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores File objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Files_File();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Folder objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Files_Folder();
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
