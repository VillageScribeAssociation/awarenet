<?

	require_once($installPath . 'modules/users/models/friendships.mod.php');
	require_once($installPath . 'modules/users/models/users.mod.php');

//--------------------------------------------------------------------------------------------------
//	short summary of user record, suitable for including in lists (perm:summary)
//--------------------------------------------------------------------------------------------------
// * $args['UID'] = UID of record to summarise

function users_summary($args) {
	if (authHas('users', 'summary', '') == false) { return ''; }
	if (array_key_exists('UID', $args)) {
		$u = new Users($args['UID']);
		$html = replaceLabels($u->extArray(), loadBlock('modules/users/views/summary.block.php'));
		return $html;
	}
}

//--------------------------------------------------------------------------------------------------

?>