<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all groups which a user belongs to (formatted for nav)
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of a user [string]
//opt: ntb - add navtitlebox (yes|no) [string]

function groups_listusergroupsnav($args) {
	global $db;
	global $user;
	global $theme;

	$ntb = 'yes';							//%	wrap in titlebox div by default [string]
	$html = '';								//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	//if (false == $user->authHas('groups', 'groups_group', 'list')) { return ''; }
	if (false == array_key_exists('userUID', $args)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the user's groups (if any)
	//----------------------------------------------------------------------------------------------
	$conditions = array("userUID='" . $db->addMarkup($args['userUID']) . "'");
	$range = $db->loadRange('groups_membership', '*', $conditions, "admin='yes'");

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	foreach($range as $item) { 
		$extra = '';
		if ('' != $item['position']) { $extra = "<b>Position:</b> " . $item['position'] . "<br/>"; }
		$html .= "[[:groups::summarynav::groupUID=" . $item['groupUID'] . "::extra=$extra:]]";
	}

	if ('yes' == $ntb) { $html = $theme->ntb($html, 'Groups', 'divUserGroups', 'show'); }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

