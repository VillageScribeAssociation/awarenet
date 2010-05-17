<?

	require_once($installPath . 'modules/groups/models/group.mod.php');
	require_once($installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all TODO: add school argument?
//--------------------------------------------------------------------------------------------------

function groups_listall($args) {
	$sql = "select * from groups";
	$result = dbQuery($sql);
	$html = '';
	while ($row = dbFetchAssoc($result)) {
		$html .= "[[:groups::summary::raUID=" . $row['UID'] . ":]]";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

