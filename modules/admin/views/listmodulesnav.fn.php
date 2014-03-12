<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//|	list all modules
//--------------------------------------------------------------------------------------------------
//role: admin - only administrators may use this

function admin_listmodulesnav($args) {
	global $kapenta;
	global $kapenta;
	global $theme;

	$html = '';

	//---------------------------------------------------------------------------------------------
	//	cjeck user role
	//---------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }

	//---------------------------------------------------------------------------------------------
	//	load each module and generate report
	//---------------------------------------------------------------------------------------------
	$modList = $kapenta->listModules();
	$table = array();
	$table[] = array('Module', '[x]');

	foreach($modList as $modName) {

		$model = new KModule($modName);
		$labels = $model->extArray();

		$labels['modulename'] = $modName;
		$labels['report'] = ''; //$report;
		$labels['status'] = "<span class='ajaxinactive'>:-(</span>";

		$cacheKey = "modstatus::$modName";
		$report = '';

		//------------------------------------------------------------------------------------------
		//	attempt to load memcached status
		//------------------------------------------------------------------------------------------
		if ((true == $kapenta->mcEnabled) && (true == $kapenta->cacheHas($cacheKey))) {		
			if ('installed' == $kapenta->cacheGet($cacheKey)) {
				$labels['status'] = "<span class='ajaxmsg'>:-)</span>";
				$report = "not generated";
			}
		}

		//------------------------------------------------------------------------------------------
		//	not found
		//------------------------------------------------------------------------------------------

		if ('' == $report) {	
			$report = $model->getInstallStatusReport();

			if (
				(strpos($report, "<!-- installed correctly -->") != false) ||
				(strpos($report, "<!-- module installed correctly -->") != false)
			) {
				$labels['status'] = "<span class='ajaxmsg'>:-)</span>";
				$kapenta->cacheSet($cacheKey, 'installed');
			} else {
				$kapenta->cacheSet($cacheKey, 'error');
			}

		}


		$nameLink = "<a href='" . $labels['editUrl'] . "'>" . $modName . "</a>";

		$table[] = array($nameLink, $labels['status']);
		
	}


	$html = $theme->arrayToHtmlTable($table, true, true);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
