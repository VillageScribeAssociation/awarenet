<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return a group's name
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or groups entry [string]
//opt: groupUID - overrides $raUID [string]
//opt: link = link to this record? (yes|no) (default is 'no') [string]

function groups_name($args) {
	$link = 'no';
	if (array_key_exists('groupUID', $args)) { $args['raUID'] = $args['groupUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	if (array_key_exists('link', $args) == false) { $link = $args['link']; }

	$model = new Groups_Group($args['raUID']);	
	if ($link == 'no') {
		return $model->name;
	} else {
		return "<a href='/groups/" . $model->alias . "'>"
			  . $model->name . "</a>";
	}
}

//--------------------------------------------------------------------------------------------------

?>

