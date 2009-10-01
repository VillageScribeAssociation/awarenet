<?

	require_once($installPath . 'modules/groups/models/groups.mod.php');
	require_once($installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//	return a group's name
//--------------------------------------------------------------------------------------------------
// * $args['groupUID'] = overrides $raUID
// * $args['raUID'] = recordAlias or UID or groups entry
// * $args['link'] = link to this record?

function groups_name($args) {
	$link = 'no';
	if (array_key_exists('groupUID', $args)) { $args['raUID'] = $args['groupUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	if (array_key_exists('link', $args) == false) { $link = $args['link']; }
	$model = new Group(sqlMarkup($args['raUID']));	
	if ($link == 'no') {
		return $model->data['name'];
	} else {
		return "<a href='/groups/" . $model->data['recordAlias'] . "'>"
			  . $model->data['name'] . "</a>";
	}
}

//--------------------------------------------------------------------------------------------------

?>