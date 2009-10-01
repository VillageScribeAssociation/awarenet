<?

	require_once($installPath . 'modules/files/models/files.mod.php');

//--------------------------------------------------------------------------------------------------
//	make a list of x files, ordered by date uploaded to system
//--------------------------------------------------------------------------------------------------
// * $args['page'] = results page
// * $args['refMod'] = module to list on
// * $args['num'] = number of files per page

function files_listall($args) {
	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (authHas('files', 'list', '') == false) { return false; }
	$start = 0; $num = 30; $page = 1;
	if (array_key_exists('num', $args)) { $num = $args['num']; }
	if (array_key_exists('page', $args)) { 
		$page = $args['page']; 
		$start = ($page - 1) * $num;
	}

	//----------------------------------------------------------------------------------------------
	//	load the files
	//----------------------------------------------------------------------------------------------
	$list = dbLoadRange('files', '*', '', 'createdOn', $num, $start);
	foreach($list as $UID => $row) {
		$model = new file();
		$model->loadArray($row);
		$html .= replaceLabels($model->extArray(), loadBlock('modules/blog/summary.block.php'));
	}  
	return $html;	
	
}

//--------------------------------------------------------------------------------------------------

?>