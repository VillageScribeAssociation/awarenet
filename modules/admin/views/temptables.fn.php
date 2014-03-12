<?

//--------------------------------------------------------------------------------------------------
//*	list tmp tables, used by upgrades, batch jobs, etc
//--------------------------------------------------------------------------------------------------

function admin_temptables($args) {
	global $kapenta;
	global $kapenta;
	global $theme;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role and any arguments
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
	// ^^ add arg checks here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$tables = $kapenta->db->loadTables();
	$list = array();
	$list[] = array('Table', 'Rows', '[x]');

	$block = $theme->loadBlock('modules/admin/views/deltmptableform.block.php');

	foreach($tables as $tableName) {
		if ('tmp_' == substr($tableName, 0, 4)) {
			//--------------------------------------------------------------------------------------
			//	count rows
			//--------------------------------------------------------------------------------------
			$rowCount = '???';
			$sql = "select count(UID) as rowCount from $tableName";
			$result = $kapenta->db->query($sql);
			if (false != $result) {
				$ary = $kapenta->db->fetchAssoc($result);
				$rowCount = $ary['rowCount'];
			}

			//--------------------------------------------------------------------------------------
			//	make the form
			//--------------------------------------------------------------------------------------
			$form = str_replace('%%tableName%%', $tableName, $block);
			$list[] = array($tableName, $rowCount, $form);
		}
	}

	$html = $theme->arrayToHtmlTable($list, true, true);
	return $html;
}

?>
