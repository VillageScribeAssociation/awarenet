<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return number of members in group
//--------------------------------------------------------------------------------------------------
//arg: UID - overrides groupUID [string]
//opt: groupUID - UID of a group (NOT alais) [string]

function groups_membercount($args) {
	global $user;
	$memberCount = '0';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('groupUID', $args)) { $args['UID'] = $args['groupUID']; }
	if (false == array_key_exists('UID', $args)) { return ''; }

	$model = new Groups_Group($args['groupUID']);
	if (false == $model->loaded) { return '(unkown group)'; }
	if (false == $user->authHas('groups', 'groups_group', 'show', $model->UID)) { return ''; }

	$memberCount = '' . count($model->members);

	return $memberCount;
}

//--------------------------------------------------------------------------------------------------

?>
