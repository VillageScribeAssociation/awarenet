<?

//--------------------------------------------------------------------------------------------------
//|	basic stats about the p2p_gifts table
//--------------------------------------------------------------------------------------------------
//opt: peerUID - UID of a P2P_Peer object [string]

function p2p_stats($args) {
	global $user;
	global $theme;
	global $db;

	$html = '';								//%	return value [string]
	$filter = '';							//%	result set filter [string]
	//----------------------------------------------------------------------------------------------
	//	check user role and any arguments
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	if (true == array_key_exists('peerUID', $args)) {
		$filter = "where peer='" . $db->addMarkup($args['peerUID']) . "'";
	}

	//----------------------------------------------------------------------------------------------
	//	query database and make the block
	//----------------------------------------------------------------------------------------------
	$sql = "select type, status, count(UID) as num from p2p_gift $filter group by type, status";
	$result = $db->query($sql);

	//----------------------------------------------------------------------------------------------
	//	query database and make the block
	//----------------------------------------------------------------------------------------------
	$table = array(array('Type', 'Status', 'Total'));
	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$table[] = array($row['type'], $row['status'], $row['num']);
	}

	$html = $theme->arrayToHtmlTable($table, true, true);
	return $html;
}

?>
