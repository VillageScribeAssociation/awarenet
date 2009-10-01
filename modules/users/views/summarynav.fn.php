<?

	require_once($installPath . 'modules/users/models/friendships.mod.php');
	require_once($installPath . 'modules/users/models/users.mod.php');

//--------------------------------------------------------------------------------------------------
//	short summary of user record formatted or the nav bar (300px wide)
//--------------------------------------------------------------------------------------------------
// * $args['UID'] = UID of record to summarise
// * $args['userUID'] = overrides UID
// * $args['extra'] = so that modules can afdd something to this summary
// * $args['target'] = for iFrames (optional)

function users_summarynav($args) {
	if (authHas('users', 'summary', '') == false) { return ''; }
	if (array_key_exists('userUID', $args) == true) { $args['UID'] = $args['userUID']; }
	if (array_key_exists('UID', $args) == false) { return false; }

	$u = new Users($args['UID']);
	$labels = $u->extArray();
	$labels['extra'] = '';
	if (array_key_exists('extra', $args)) { $labels['extra'] = $args['extra']; }

	$html = replaceLabels($labels, loadBlock('modules/users/views/summarynav.block.php'));

	if (array_key_exists('target', $args) == true) {
		$html = str_replace("<a href=", "<a target='" . $args['target'] . "' href=", $html);
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>