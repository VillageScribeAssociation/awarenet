<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//|	list all modules
//--------------------------------------------------------------------------------------------------
//role: admin - only administrators may do this

function admin_listmodules($args) {
		global $kapenta;
		global $theme;
		global $user;

	$html = '';

	//---------------------------------------------------------------------------------------------
	//	check user role
	//---------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	//---------------------------------------------------------------------------------------------
	//	load each module and generate report
	//---------------------------------------------------------------------------------------------
	$modList = $kapenta->listModules();
	$block = $theme->loadBlock("modules/admin/views/moduleinstallstatus.block.php");

	foreach($modList as $modName) { 
		$model = new KModule($modName);
		$labels = $model->extArray();
		$report = $model->getInstallStatusReport();

		$labels['modulename'] = $modName;
		$labels['report'] = ''; //$report;
		$labels['status'] = "<span class='ajaxerror'>not installed</span>";

		if (strpos($report, "<!-- installed correctly -->") != false) 
			{ $labels['status'] = "<span class='ajaxmsg'>installed</span>"; }


		$title = "<a href='%%serverPath%%mods/" . $modName . "'>$modName</a>" . $labels['status'];

		$html .= $theme->replaceLabels($labels, $block);
		
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
