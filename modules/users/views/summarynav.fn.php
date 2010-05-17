<?

	require_once($installPath . 'modules/users/models/friendship.mod.php');
	require_once($installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	short summary of user record formatted or the nav bar (300px wide)
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of record to summarise [string]
//opt: userUID - overrides UID [string]
//opt: extra - add something to this summary [string]
//opt: target - a URL or _parent, for iFrames [string]

function users_summarynav($args) {
	if (authHas('users', 'summary', '') == false) { return ''; }
	if (array_key_exists('userUID', $args) == true) { $args['UID'] = $args['userUID']; }
	if (array_key_exists('UID', $args) == false) { return false; }

	$model = new User($args['UID']);
	$labels = $model->extArray();
	$labels['extra'] = '';

	if (array_key_exists('extra', $args)) { $labels['extra'] = $args['extra']; }

	$html = replaceLabels($labels, loadBlock('modules/users/views/summarynav.block.php'));

	if (array_key_exists('target', $args) == true) 
		{ $html = str_replace("<a href=", "<a target='" . $args['target'] . "' href=", $html); }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
