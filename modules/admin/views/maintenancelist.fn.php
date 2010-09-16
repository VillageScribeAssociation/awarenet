<?

//-------------------------------------------------------------------------------------------------
//|	find all files in this project which should be tracked by subversion
//-------------------------------------------------------------------------------------------------
//role: admin - only administrators may use this

function admin_maintenancelist($args) {
	global $kapenta, $user;

	$maint = array();
	$count = 0;
	$mods = $kapenta->listModules();
	$html = '';

	//---------------------------------------------------------------------------------------------
	//	check user role
	//---------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	//---------------------------------------------------------------------------------------------
	//	go through all modules and look for maintenance script file (assume it contains function)
	//---------------------------------------------------------------------------------------------
	foreach($mods as $modName) {
		$fileName = 'modules/' . $modName . '/inc/maintenance.inc.php';
		$maint[$modName] = $kapenta->fileExists($fileName);
		if (true == $maint[$modName]) { $count++; }
	}

	//---------------------------------------------------------------------------------------------
	//	make html list
	//---------------------------------------------------------------------------------------------
	$html .= "<h2>Module Maintenance Scripts</h2>\n";
	foreach ($maint as $modName => $hasMaint) {
		if (true == $hasMaint) {
			$url = '%%serverPath%%admin/maintenance/' . $modName;
			$html .= "<a href='" . $url . "'>[ Maintance: $modName module &gt;&gt; ]</a><br/>";
		}
	}

	return $html;
}

?>

