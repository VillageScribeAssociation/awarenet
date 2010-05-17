<?

//--------------------------------------------------------------------------------------------------
//	install scripts for forums module
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/forums/models/forum.mod.php');
require_once($installPath . 'modules/forums/models/forumthread.mod.php');
require_once($installPath . 'modules/forums/models/forumreply.mod.php');

//--------------------------------------------------------------------------------------------------
//	install the gallery module
//--------------------------------------------------------------------------------------------------
//returns: html report [string]

function forums_install_module() {
	global $user;

	if ($user->data['ofGroup'] != 'admin') { return false; }	// only admins can do this

	$report .= "<h3>Installing Forums Module</h3>\n";

	//------------------------------------------------------------------------------------------
	//	create forums table if it does not exist, upgrade it if it does
	//------------------------------------------------------------------------------------------
	$model = new Forum();
	$dbSchema = $model->initDbSchema();
	$report .= dbInstallTable($dbSchema);	

	//------------------------------------------------------------------------------------------
	//	create forumthreads table if it does not exist, upgrade it if it does
	//------------------------------------------------------------------------------------------
	$model = new ForumThread();
	$dbSchema = $model->initDbSchema();
	$report .= dbInstallTable($dbSchema);	

	//------------------------------------------------------------------------------------------
	//	create forumreplies table if it does not exist, upgrade it if it does
	//------------------------------------------------------------------------------------------
	$model = new ForumReply();
	$dbSchema = $model->initDbSchema();
	$report .= dbInstallTable($dbSchema);	

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

function forums_install_status_report() {
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }	// only admins can do this

	$installed = true;

	//---------------------------------------------------------------------------------------------
	//	ensure that the forums table exists and is correct
	//---------------------------------------------------------------------------------------------
	$model = new Forum();
	$dbSchema = $model->initDbSchema();

	if (dbTableExists($dbSchema['table']) == true) {
		//-----------------------------------------------------------------------------------------
		//	table present
		//-----------------------------------------------------------------------------------------
		$extantSchema = dbGetSchema($dbSchema['table']);

		if (dbCompareSchema($dbSchema, $extantSchema) == false) {
			//-------------------------------------------------------------------------------------
			// table schemas DO NOT match (fail)
			//-------------------------------------------------------------------------------------
			$installed = false;		
			$report .= "<p>A '" . $dbSchema['table'] . "' table exists, but does not match "
					 . "object's schema.</p>\n"
					 . "<b>Object Schema:</b><br/>\n" . dbSchemaToHtml($dbSchema) . "<br/>\n"
					 . "<b>Extant Table:</b><br/>\n" . dbSchemaToHtml($extantSchema) . "<br/>\n";

		} else {
			//-------------------------------------------------------------------------------------
			// table schemas match
			//-------------------------------------------------------------------------------------
			$report .= "<p>'" . $dbSchema['table'] . "' table exists, matches object schema.</p>\n"
					 . "<b>Database Table:</b><br/>\n" . dbSchemaToHtml($dbSchema) . "<br/>\n";

		}

	} else {
		//-----------------------------------------------------------------------------------------
		//	table missing (fail)
		//-----------------------------------------------------------------------------------------
		$installed = false;
		$report .= "<p>'" . $dbSchema['table'] . "' table does not exist in the database.</p>\n"
				 . "<b>Object Schema:</b><br/>\n" . dbSchemaToHtml($dbSchema) . "<br/>\n";
	}


	//---------------------------------------------------------------------------------------------
	//	ensure that the forumthreads table exists and is correct
	//---------------------------------------------------------------------------------------------
	$model = new ForumThread();
	$dbSchema = $model->initDbSchema();
	$report .= dbGetTableInstallStatus($dbSchema);

	

	return $report;
}


function install_forums_module() {
	global $installPath;
	global $user;

	if ($user->data['ofGroup'] != 'admin') { return false; }

	//----------------------------------------------------------------------------------------------
	//	main forums table
	//----------------------------------------------------------------------------------------------
	$model = new Forum();
	$report = $model->install();

	//----------------------------------------------------------------------------------------------
	//	threads table
	//----------------------------------------------------------------------------------------------
	$model = new ForumThread();
	$report .= $model->install();

	//----------------------------------------------------------------------------------------------
	//	replies table
	//----------------------------------------------------------------------------------------------
	$model = new ForumReply();
	$report .= $model->install();

	return $report;
}

?>
