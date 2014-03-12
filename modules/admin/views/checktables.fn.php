<?

//--------------------------------------------------------------------------------------------------
//|	check database tables
//--------------------------------------------------------------------------------------------------

function admin_checktables($args) {
	global $kapenta;
	global $kapenta;
	global $theme;

	//----------------------------------------------------------------------------------------------
	//	check user role and any arguments
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	get table state
	//----------------------------------------------------------------------------------------------
	$range = array();
	
	$tables = $kapenta->db->listTables();
	foreach ($tables as $table) {
		$sql = "CHECK TABLE `" . $table . "`;";
		$result = $kapenta->db->query($sql);
		while($row = $kapenta->db->fetchAssoc($result)) { 
			$row['tableName'] = $table;
			$range[] = $row;
		}
	}
	
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/admin/views/repairtablebutton.block.php');

	$table = array();
	$table[] = array('Table', 'Op', 'Msg_type', 'Msg_text', '[x]');
	foreach($range as $item) { 
		$btn = '';
		if ('ok' != strtolower($item['Msg_text'])) { $btn = $theme->replaceLabels($item, $block); }
		$table[] = array($item['Table'], $item['Op'], $item['Msg_type'], $item['Msg_text'], $btn);
	}
	$html = $theme->arrayToHtmlTable($table, true, true);

	return $html;
}

?>
