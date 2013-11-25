<?

	require_once($kapenta->installPath . 'modules/code/models/bugs.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/code.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/codeproject.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/coderevision.mod.php');

//--------------------------------------------------------------------------------------------------
//	list of all items
//--------------------------------------------------------------------------------------------------

function code_listallnav($args) {
	global $db;

	global $kapenta;
	$html = '';

	$sql = "select UID, title, recordAlias from code";
	$result = $db->query($sql);
	while($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$link = $kapenta->serverPath . 'code/' . $row['recordAlias'];
		$html .= "<a href='" . $link . "' class='black'>" . strtoupper($row['title']) . "</a><br/>";
	}
	return $html;
}


//--------------------------------------------------------------------------------------------------

?>