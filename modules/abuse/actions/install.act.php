<?

	require_once($kapenta->installPath . 'modules/abuse/models/report.mod.php');

//--------------------------------------------------------------------------------------------------
//*	temp action to install the abuse module
//--------------------------------------------------------------------------------------------------
//TODO: make standard install script

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	create the table
	//----------------------------------------------------------------------------------------------
	$model = new Abuse_Report();
	$dbSchema = $model->getDbSchema();

	$db->createTable($dbSchema);
	if (true == $db->tableExists('abuse_report')) {	
		echo "Abuse report module installed.<br/>\n"; 
	} else {
		echo "Could not create table 'abuse_report'.<br/>\n";
	}

?>
