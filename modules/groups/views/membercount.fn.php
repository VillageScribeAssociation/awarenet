<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return number of members in group
//--------------------------------------------------------------------------------------------------
//arg: groupUID - UID of a group (NOT alais) [string]
//opt: UID - overrides groupUID [string]

function groups_membercount($args) {
	global $user;
	$memberCount = '0';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('UID', $args)) { $args['groupUID'] = $args['UID']; }
	if (false == array_key_exists('groupUID', $args)) { return ''; }

	$model = new Groups_Group($args['raUID']);

	if (false == $user->authHas('groups', 'groups_group', 'show', $model->UID)) { return false; }

	$memberCount = '' . count($model->members);

	return $memberCount;
}

//--------------------------------------------------------------------------------------------------

?>

