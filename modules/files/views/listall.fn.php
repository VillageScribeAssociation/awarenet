<?

	require_once($installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make a list of x files, ordered by date uploaded to system
//--------------------------------------------------------------------------------------------------
//opt: refModule - module to list on [string]
//opt: page - results page [string]
//opt: num - number of files per page [string]

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

