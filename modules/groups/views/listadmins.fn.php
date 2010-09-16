<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list administrators of a group
//--------------------------------------------------------------------------------------------------
//arg: groupUID - UID of a group [string]
//opt: target = link target (for frames, etc) [string]

function groups_listadmins($args) {
	global $user;
	$target = '';
	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('groupUID', $args)) { $args['raUID'] = $args['groupUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }
	if (true == array_key_exists('target', $args)) { $target = '::target=' . $args['target']; }

	$model = new Groups_Group($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('groups', 'Groups_Group', 'show', $model->UID)) { return ''; }
	$members = $model->getMembers();

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------	
	$admins = array();
	//TODO: more checks here
	foreach ($members as $row) {
		if ('yes' == $row['admin']) 
			{ $admins[] = '[[:users::namelink::userUID=' . $row['userUID'] . $target . ':]]'; }
	}	
	
	if (0 == count($admins))
		{ $html = "(This group has no administrators, only sysadmins can edit it)<br/>"; }

	if (count($admins) >= 1) {
		$html .= "This group is administered by ";
		$count = count($admins);
		foreach ($admins as $admin) {
			$html .= $admin;
			$count--;
			if ($count >= 2) { $html .= ", "; }
			if ($count == 1) { $html .= " and "; }
			if ($count == 0) { $html .= "."; }
		}

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

