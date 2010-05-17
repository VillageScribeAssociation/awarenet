<?

	require_once($installPath . 'modules/groups/models/group.mod.php');
	require_once($installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	returns whether current user is a group admin
//--------------------------------------------------------------------------------------------------
//arg: raUID - group UID or recordAlias [string]

function groups_haseditauth($args) {
	global $user;
	if ($user->data['ofGroup'] == 'admin') { return 'yes'; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Group($args['raUID']);
	if ($model->hasEditAuth($user->data['UID']) == true) { return 'yes'; }
	return 'no';
}


?>

