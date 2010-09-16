<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list group members for the nav (300 px wide)
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or groups entry [string]
//opt: groupUID - overrides raUID [string]
//opt: editmode - set to yes to show 'remove member' links [string]

function groups_listmembersnav($args) {
	global $user;
	$editmode = 'no';
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('editmode', $args)) { $editmode = $args['editmode']; }
	if (true == array_key_exists('groupUID', $args)) { $args['raUID'] = $args['groupUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Groups_Group($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('groups', 'Groups_Group', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$members = $model->getMembers();
	$isAdmin = $model->hasEditAuth($user->UID);		//TODO: use permission for this

	foreach($members as $mbr) {
		$html .= "[[:users::summarynav::userUID=" . $mbr['userUID']
			  . "::extra=(" . $mbr['position'] . ")::target=_parent:]]\n";

		$userUID = $mbr['userUID'];
		if ( (true == $isAdmin) && ($userUID != $user->UID) && ('yes' == $editmode) ) {
			$rmUrl = "%%serverPath%%groups/editmembers/removemember_" . $userUID . "/" . $args['groupUID'];
			$html .= "<a href='" . $rmUrl . "'>[ remove member &gt;&gt; ]</a><br/><br/>";
		}

	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

