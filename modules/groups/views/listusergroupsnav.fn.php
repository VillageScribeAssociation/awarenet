<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all groups which a user belongs to (formatted for nav)
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of a user [string]

function groups_listusergroupsnav($args) {
	global $db, $user;
	$html = '';								//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('groups', 'Groups_Group', 'list')) { return ''; }
	if (false == array_key_exists('userUID', $args)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the user's groups (if any)
	//----------------------------------------------------------------------------------------------
	$conditions = array("userUID='" . $db->addMarkup($args['userUID']) . "'");
	$range = $db->loadRange('Groups_Membership', '*', $conditions, "admin='yes'");

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	foreach($range as $row) 
		{ $html .= "[[:groups::summarynav::groupUID=" . $row['groupUID'] . ":]]"; }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

