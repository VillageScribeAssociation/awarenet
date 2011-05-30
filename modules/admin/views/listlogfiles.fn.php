<?

//--------------------------------------------------------------------------------------------------
//|	lists XML log files of various types
//--------------------------------------------------------------------------------------------------
//opt: type - type of log file to display [string]

function admin_listlogfiles($args) {
	global $kapenta;
	global $user;

	$html = '';						//%	return value [string]
	$type = 'pageview.log.php';		//%	type of log [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (true == array_key_exists('type', $args)) { $type = $args['type']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$files = $kapenta->listFiles('data/log/', $type);
	foreach($files as $file) {
		$viewLink = "<a href='%%serverPath%%admin/log/" . $file . "'>" . $file. "</a>";
		$xmlLink = "<a href='%%serverPath%%admin/log/format_xml/" . $file . "'>[xml]</a>";
		$delLink = "<a href='%%serverPath%%admin/logs/delete_" . $file . "'>[delete]</a>";
		$html .= $viewLink . ' ' . $xmlLink . ' ' . $delLink . "<br/>\n";
	}

	return $html;
}

?>
