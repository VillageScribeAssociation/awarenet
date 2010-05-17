<?

//-------------------------------------------------------------------------------------------------
//|	find all files in this project which should be tracked by subversion
//-------------------------------------------------------------------------------------------------

function admin_maintenancelist($args) {
	global $user;
	global $installPath;
	global $serverPath;

	$maint = array();
	$count = 0;
	$mods = listModules();
	$html = '';

	if ($user->data['ofGroup'] != 'admin') { return ''; }

	//---------------------------------------------------------------------------------------------
	//	go through all modules and look for maintenance script file (assume it contains function)
	//---------------------------------------------------------------------------------------------
	foreach($mods as $modName) {
		$fileName = $installPath . 'modules/' . $modName . '/inc/maintenance.inc.php';
		$maint[$modName] = file_exists($fileName);
		if (true == $maint[$modName]) { $count++; }
	}

	//---------------------------------------------------------------------------------------------
	//	make html list
	//---------------------------------------------------------------------------------------------
	$html .= "<h2>Module Maintenance Scripts</h2>\n";
	foreach ($maint as $modName => $hasMaint) {
		if (true == $hasMaint) {
			$url = $serverPath . 'admin/maintenance/' . $modName;
			$html .= "<a href='" . $url . "'>[ Maintance: $modName module &gt;&gt; ]</a><br/>";
		}
	}

	return $html;
}

?>

