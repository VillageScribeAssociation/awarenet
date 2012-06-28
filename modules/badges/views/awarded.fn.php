<?

//--------------------------------------------------------------------------------------------------
//|	list all badges awarded to a user
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of a Users_User object [string]

function badges_awarded($args) {
	global $db, $user, $theme;
	$html = '';			//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	//TODO: permissions check here
	if (false == array_key_exists('userUID', $args)) { return '(userUID not supplied)'; }

	//----------------------------------------------------------------------------------------------
	//	query database and make the block
	//----------------------------------------------------------------------------------------------
	$conditions = array("userUID='" . $db->addMarkup($args['userUID']) . "'");	
	$range = $db->loadRange('badges_userindex', '*', $conditions, 'createdOn DESC');

	if (count($range) == 0) { return ''; }

	$imgOpts = "size=thumb::link=no::refModule=badges::refModel=badges_badge::display=inline::size=thumb90::";

	$html .= ''
	 . "[[:theme::navtitlebox::label=Badges::toggle=divUserBadges:]]\n"
	 . "<div id='divUserBadges'>"
	 . "<div class='spacer'></div>\n";

	foreach($range as $row) {
		$block = ''
		 . "[[:images::default"
		 . "::link=no"
		 . "::refModule=badges"
		 . "::refModel=badges_badge"
		 . "::refUID=" . $row['badgeUID']
		 . "::display=inline"
		 . "::size=thumb90"
		 . ":]]\n";

		$badgeUrl = '%%serverPath%%badges/' . $row['badgeUID'];
		$html .= "<a href='$badgeUrl'>$block</a>\n";
	}

	$html .= "<div class='foot'></div>\n</div>\n<br/>";

	return $html;
}

?>
