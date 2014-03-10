<?

	require_once($kapenta->installPath . 'modules/code/models/bugs.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/code.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/codeproject.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/coderevision.mod.php');

//--------------------------------------------------------------------------------------------------
//	list all
//--------------------------------------------------------------------------------------------------

function code_listall($args) {
	global $kapenta;

	$sql = "select * from code order by refno DESC";
	$result = $kapenta->db->query($sql);
	$html = '';
	while ($row = $kapenta->db->fetchAssoc($result)) {
		$html .= "[[:code::summary::raUID=" . $row['UID'] . ":]]";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>