<?

	require_once($kapenta->installPath . 'modules/code/models/bugs.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/code.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/codeproject.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/coderevision.mod.php');

//--------------------------------------------------------------------------------------------------
//	list all
//--------------------------------------------------------------------------------------------------

function code_listall($args) {
	global $db;

	$sql = "select * from code order by refno DESC";
	$result = $db->query($sql);
	$html = '';
	while ($row = $db->fetchAssoc($result)) {
		$html .= "[[:code::summary::raUID=" . $row['UID'] . ":]]";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>