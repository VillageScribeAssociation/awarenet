<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//|	list all modules
//--------------------------------------------------------------------------------------------------
//role: admin - only administrators may use this

function admin_listmodulesnav($args) {
	global $kapenta, $user, $theme;
	$html = '';

	//---------------------------------------------------------------------------------------------
	//	cjeck user role
	//---------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	//---------------------------------------------------------------------------------------------
	//	load each module and generate report
	//---------------------------------------------------------------------------------------------
	$modList = $kapenta->listModules();
	$table = array();
	$table[] = array('Module', '[x]');

	foreach($modList as $modName) { 
		$model = new KModule($modName);
		$labels = $model->extArray();
		$report = $model->getInstallStatusReport();

		$labels['modulename'] = $modName;
		$labels['report'] = ''; //$report;
		$labels['status'] = "<span class='ajaxinactive'>:-(</span>";

		if (strpos($report, "<!-- installed correctly -->") != false) 
			{ $labels['status'] = "<span class='ajaxmsg'>:-)</span>"; }

		if (strpos($report, "<!-- module installed correctly -->") != false) 
			{ $labels['status'] = "<span class='ajaxmsg'>:-)</span>"; }

		$nameLink = "<a href='" . $labels['editUrl'] . "'>" . $modName . "</a>";

		$table[] = array($nameLink, $labels['status']);
		
	}

	$html = $theme->arrayToHtmlTable($table, true, true);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
