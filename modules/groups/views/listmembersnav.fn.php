<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list group members for the nav (300 px wide)
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or groups entry [string]
//opt: groupUID - overrides raUID [string]
//opt: editmode - set to yes to show 'remove member' links, js for ajax version (yes|no|js) string]
//TODO: tidy this

function groups_listmembersnav($args) {
	global $user;
	global $page;

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
	if (false == $user->authHas('groups', 'groups_group', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$members = $model->getMembers();
	$isAdmin = $model->hasEditAuth($user->UID);		//TODO: use permission for this

	$channel = 'members-' . $model->UID;
	$page->setTrigger('groups', $channel, $args['rawblock']);

	$divId = 'divMembers' . $model->UID;
	$html .= "<div id='$divId'>\n";

	foreach($members as $member) {
		$block = '[[:users::summarynav'
			 . '::userUID=' . $member['userUID']
			 . '::extra=(' . $member['position'] . ")"
			 . '::target=_parent:]]';

		$userUID = $member['userUID'];
		if (true == $isAdmin) {
			//--------------------------------------------------------------------------------------
			//	basic HTML link to remove members
			//--------------------------------------------------------------------------------------
			if ('yes' == $editmode) {
				$rmUrl = "groups/editmembers/removemember_" . $userUID . "/" . $model->UID;
				$html .= $block
					 . "<a href='%%serverPath%%" . $rmUrl . "'>[ remove member &gt;&gt; ]</a>"
					 . "<br/><br/>\n";
			}

			//--------------------------------------------------------------------------------------
			//	call to AJAX client for removing members
			//--------------------------------------------------------------------------------------
			if ('js' == $editmode) {
				//TODO: tidy this, perhaps make this a block
				$imgUrl = '%%serverPath%%themes/clockface/icons/arrow_x_green.png';
				$html .= "
					<table noborder>
						<tr>
							<td valign='top'>
								<a 
									href='javascript:void(0);'
									onClick=\"groups_removeMember('" . $userUID . "');\" 
									title='Remove member.'
								><img src='$imgUrl' /></a>
							</td>
							<td valign='top'>
								$block
								<div id='divMemberStatus" . $userUID . "'></div>
							</td>
						</tr>
					</table>\n";
			}

			//--------------------------------------------------------------------------------------
			//	display memberships only
			//--------------------------------------------------------------------------------------
			if (('' == $editmode) || ('no' == $editmode)) { $html .= $block . "\n"; }

		} else {
			$html .= $block . "\n";
		}
	}

	$html .= "</div><!-- REGISTERBLOCK:$divId:" . base64_encode($args['rawblock']) . " -->";

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

