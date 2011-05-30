<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all TODO: add school argument?
//--------------------------------------------------------------------------------------------------
//TODO: fix this up

function groups_listall($args) {
	global $db;
	$html = '';

	$sql = "select * from groups_group";
	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) 
		{ $html .= "[[:groups::summary::raUID=" . $row['UID'] . ":]]"; }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
