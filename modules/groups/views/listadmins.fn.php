<?

	require_once($installPath . 'modules/groups/models/group.mod.php');
	require_once($installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list administrators of a group
//--------------------------------------------------------------------------------------------------
//arg: groupUID - UID of a group [string]
//opt: target = link target (for frames, etc) [string]

function groups_listadmins($args) {
	if (authHas('groups', 'show', '') == false) { return false; }
	if (array_key_exists('groupUID', $args)) { $args['raUID'] = $args['groupUID']; }

	$target = '';
	if (array_key_exists('target', $args)) { $target = '::target=' . $args['target']; }

	$model = new Group($args['raUID']);
	$members = $model->getMembers();
	
	$admins = array();

	foreach ($members as $row) {
		if ($row['admin'] == 'yes') 
			{ $admins[] = '[[:users::namelink::userUID=' . $row['userUID'] . $target . ':]]'; }
	}	
	
	if (count($admins) == 0) { 
		$html = "(This group has no administrators, only sysadmins can edit it)<br/>"; 
	}

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

