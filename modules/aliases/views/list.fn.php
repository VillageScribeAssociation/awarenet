<?

	require_once($kapenta->installPath . 'modules/home/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//|	paginated list of all aliases matching criteria
//--------------------------------------------------------------------------------------------------
//opt: page - page no to display (default is 1) [string]
//opt: num - number of records per page (default 300) [string]

function aliases_list($args) {
		global $kapenta;
		global $kapenta;
		global $kapenta;
		global $theme;
		global $user;


	$num = 100;					//%	number of items per page [int]
	$pageNo = 1;				//%	page number starts at 1 [int]
	$start = 0;					//%	starting position within SQL results [int]
	$html = '';					//%	return value [string]

	$fModule = '*';				//%	filter to module if not '*' [string]
	$fModel = '*';				//%	filter to model if not '*' [string]
	$fUID = '*';				//%	filter to UID if not '*' [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('aliases', 'aliases_alias', 'show', '')) { return ''; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('page', $args)) { 
		$pageNo = (int)$args['page']; 
		$start = ($pageNo - 1) * $num;
	}

	if ((true == array_key_exists('filterModule', $args)) && ('*' != $args['filterModule'])) {
		if (true == $kapenta->moduleExists($args['filterModule'])) {
			$fModule = $args['filterModule'];
		}
	}

	if ((true == array_key_exists('filterModel', $args)) && ('*' != $args['filterModel'])) {
		if (true == $kapenta->db->tableExists($args['filterModel'])) { $fModel = $args['filterModel']; }
	}

	if ((true == array_key_exists('filterUID', $args)) && ('*' != $args['filterUID'])) {
		$fUID = $args['filterUID']; 
	}

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	if ('*' != $fModule) { $conditions[] = "refModule='" . $kapenta->db->addMarkup($fModule) . "'"; }
	if ('*' != $fModel) { $conditions[] = "refModel='" . $kapenta->db->addMarkup($fModel) . "'"; }
	if ('*' != $fUID) { $conditions[] = "refUID='" . $kapenta->db->addMarkup($fUID) . "'"; }
	
	$total = $kapenta->db->countRange('aliases_alias', $conditions);

	$totalPages = ceil($total / $num);
	if (($pageNo - 1) > $totalPages) { $pageNo = $totalPages; }

	$start = (($pageNo - 1) * $num);

	$range = $kapenta->db->loadRange('aliases_alias', '*', $conditions, 'refModule, refModel, refUID', $num, $start);
	
	//----------------------------------------------------------------------------------------------
	//	make the table
	//----------------------------------------------------------------------------------------------

	$table = array();
	$table[] = array('Module', 'Model', 'UID', 'Alias', 'Default');
	foreach($range as $it) {
		$default = 'unk';
		$table[] = array($it['refModule'], $it['refModel'], $it['refUID'], $it['alias'], $default);
	}

	$html .= $theme->arrayToHtmlTable($table, true, true);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
