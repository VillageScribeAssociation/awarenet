<?

	require_once($installPath . 'modules/groups/models/groups.mod.php');
	require_once($installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//	list group members for the nav (300 px wide)
//--------------------------------------------------------------------------------------------------
// * $args['groupUID'] = overrides raUID
// * $args['raUID'] = recordAlias or UID or groups entry
// * $args['editmode'] = set to yes to show 'remove member' links

function groups_listmembersnav($args) {
	global $user;
	$editmode = 'no';
	if (authHas('groups', 'show', '') == false) { return false; }
	if (array_key_exists('editmode', $args) == true) { $editmode = $args['editmode']; }
	if (array_key_exists('groupUID', $args)) { $args['raUID'] = $args['groupUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }

	$model = new Group($args['raUID']);
	$members = $model->getMembers();
	$html = '';

	$isAdmin = $model->hasEditAuth($user->data['UID']);

	foreach($members as $mbr) {
		$html .= "[[:users::summarynav::userUID=" . $mbr['userUID']
			  . "::extra=(" . $mbr['position'] . ")::target=_parent:]]\n";

		$userUID = $mbr['userUID'];
		if ( (true == $isAdmin) && ($userUID != $user->data['UID']) && ('yes' == $editmode) ) {
			$rmUrl = "%%serverPath%%groups/editmembers/removemember_" . $userUID . "/" . $args['groupUID'];
			$html .= "<a href='" . $rmUrl . "'>[ remove member &gt;&gt; ]</a><br/><br/>";
		}

	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>