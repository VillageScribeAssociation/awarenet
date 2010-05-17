<?

	require_once($installPath . 'modules/users/models/friendship.mod.php');
	require_once($installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	short summary of user record, suitable for including in lists (perm:summary)
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of record to summarise [string]

function users_summary($args) {
	if (authHas('users', 'summary', '') == false) { return ''; }
	if (array_key_exists('UID', $args)) {
		$u = new User($args['UID']);
		$html = replaceLabels($u->extArray(), loadBlock('modules/users/views/summary.block.php'));
		return $html;
	}
}

//--------------------------------------------------------------------------------------------------

?>
