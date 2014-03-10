<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make a list of x files, ordered by date uploaded to system
//--------------------------------------------------------------------------------------------------
//opt: refModule - module to list on [string]
//opt: page - results page [string]
//opt: num - number of files per page [string]

function files_listall($args) {
		global $db;
		global $page;
		global $theme;

	$num = 30;							//%	number of items per page [int]
	$pageNo = 1;						//%	page number, starts at 1 [int]
	$start = 0;							//%	position in SQL results [int]
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == authHas('files', 'files_file', 'list')) { return false; }
	if (array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (array_key_exists('page', $args)) { 
		$pageNo = (int)$args['page']; 
		$start = ($pageNo - 1) * $num;
	}

	//----------------------------------------------------------------------------------------------
	//	load the files
	//----------------------------------------------------------------------------------------------
	$list = $db->loadRange('files_file', '*', '', 'createdOn', $num, $start);
	$block = $theme->loadBlock('modules/blog/summary.block.php');

	foreach($list as $UID => $row) {
		$model = new Files_File();
		$model->loadArray($row);
		$html .= $theme->replaceLabels($model->extArray(), $block);
	}  
	return $html;	
	
}

//--------------------------------------------------------------------------------------------------

?>
